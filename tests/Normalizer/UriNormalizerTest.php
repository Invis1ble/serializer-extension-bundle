<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle\Tests\Normalizer;

use Psr\Http\Message\UriInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Serializer;

class UriNormalizerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function itTakesPrecedenceOverPropertyNormalizer(): void
    {
        self::bootKernel(['environment' => 'serializer_test']);

        $container = static::getContainer();

        /** @var Serializer $serializer */
        $serializer = $container->get('serializer');

        $uri = $this->createMock(UriInterface::class);

        $uri->expects($this->once())
            ->method('__toString')
            ->willReturn('https://example.com');

        $this->assertSame('https://example.com', $serializer->normalize($uri));
    }
}
