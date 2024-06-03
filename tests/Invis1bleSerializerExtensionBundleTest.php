<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use Invis1ble\SerializerExtensionBundle\Invis1bleSerializerExtensionBundle;
use Invis1ble\SymfonySerializerExtension\Normalizer\UriNormalizer;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Invis1bleSerializerExtensionBundleTest extends AbstractExtensionTestCase
{
    public function testContainerHasUriFactory(): void
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasService(
            'invis1ble_serializer_extension.uri_factory',
            HttpFactory::class,
        );

        $this->assertContainerBuilderHasAlias(
            UriFactoryInterface::class,
            'invis1ble_serializer_extension.uri_factory',
        );
    }

    /**
     * @dataProvider provideNormalizer
     */
    public function testContainerHasNormalizer(string $serviceId, string $serviceFqn, int $priority): void
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasService($serviceId, $serviceFqn);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            serviceId: $serviceId,
            argumentIndex: 0,
            expectedValue: new Reference('invis1ble_serializer_extension.uri_factory'),
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            serviceId: $serviceId,
            tag: 'serializer.normalizer',
            attributes: ['priority' => $priority],
        );
    }

    /**
     * @return \Generator<array>
     */
    public static function provideNormalizer(): iterable
    {
        $bundlePrefix = 'invis1ble_serializer_extension';

        yield ["$bundlePrefix.normalizer.uri", UriNormalizer::class, 10];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.environment', 'test');
        $this->setParameter('kernel.build_dir', __DIR__);
    }

    protected function getContainerExtensions(): array
    {
        return [
            $this->createBundle()->getContainerExtension(),
        ];
    }

    private function createBundle(): AbstractBundle
    {
        return new Invis1bleSerializerExtensionBundle();
    }
}
