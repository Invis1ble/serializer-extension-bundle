<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle\Tests\Normalizer;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Uri;
use Invis1ble\SerializerExtensionBundle\Tests\Fixtures\UriController;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UriNormalizerTest extends KernelTestCase
{
    public function testItTakesPrecedenceOverPropertyNormalizer(): void
    {
        self::bootKernel(['environment' => 'serializer_test']);

        $container = static::getContainer();

        $serializer = $container->get(SerializerInterface::class);

        $propertyNormalizer = $container->get('serializer.normalizer.property');
        $this->assertInstanceOf(NormalizerInterface::class, $propertyNormalizer);

        $uri = $this->createMock(UriInterface::class);
        $this->assertTrue($propertyNormalizer->supportsNormalization($uri));

        $uri->expects($this->once())
            ->method('__toString')
            ->willReturn('https://example.com');

        $this->assertSame('https://example.com', $serializer->normalize($uri));
    }

    #[DataProvider('provideKernelOptions')]
    public function testCompiledContainerRegistersExactlyOneUriNormalizer(string $environment, bool $debug): void
    {
        self::bootKernel(['environment' => $environment, 'debug' => $debug]);
        $container = static::getContainer();

        $this->assertSame(
            ['invis1ble_serializer_extension.normalizer.uri' => [['priority' => 10]]],
            $container->getParameter('test.bundle_normalizers'),
        );

        $factory = $container->get('invis1ble_serializer_extension.uri_factory');
        $this->assertInstanceOf(HttpFactory::class, $factory);
        $this->assertSame($factory, $container->get(UriFactoryInterface::class));

        $controller = $container->get(UriController::class);
        $this->assertSame($factory, $controller->uriFactory);
        $this->assertSame($container->get('serializer'), $controller->serializer);
    }

    public static function provideKernelOptions(): iterable
    {
        yield 'debug' => ['test', true];
        yield 'property normalizer' => ['serializer_test', true];
        yield 'production' => ['prod', false];
    }

    #[DataProvider('provideUriTypes')]
    public function testUriRoundTripThroughContainerSerializer(string $type): void
    {
        self::bootKernel();
        $serializer = static::getContainer()->get(SerializerInterface::class);
        $this->assertInstanceOf(Serializer::class, $serializer);

        foreach (['https://example.com/path?foo=bar#fragment', '/relative/path', ''] as $value) {
            $uri = $serializer->denormalize($value, $type, null, ['groups' => ['uri']]);
            $this->assertInstanceOf($type, $uri);
            $this->assertSame($value, $serializer->normalize($uri, null, ['groups' => ['uri']]));

            $json = $serializer->serialize($uri, 'json');
            $this->assertSame($value, json_decode($json, true, 512, JSON_THROW_ON_ERROR));
            $restored = $serializer->deserialize($json, $type, 'json');
            $this->assertInstanceOf($type, $restored);
            $this->assertSame($value, (string) $restored);
        }
    }

    public static function provideUriTypes(): iterable
    {
        yield 'PSR interface' => [UriInterface::class];
        yield 'Guzzle URI' => [Uri::class];
    }

    public function testInvalidUriIsRejectedThroughContainerSerializer(): void
    {
        self::bootKernel();
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $this->expectException(\InvalidArgumentException::class);
        $serializer->deserialize('"https://example.com:99999"', UriInterface::class, 'json');
    }

    public function testHttpRequestUsesAutowiredSerializer(): void
    {
        $kernel = self::bootKernel();
        $value = 'https://example.com/path?foo=bar#fragment';
        $request = Request::create(
            '/uri',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($value, JSON_THROW_ON_ERROR),
        );

        $response = $kernel->handle($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame($value, json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR));

        $kernel->terminate($request, $response);
    }
}
