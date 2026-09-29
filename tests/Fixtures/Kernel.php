<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle\Tests\Fixtures;

use Invis1ble\SerializerExtensionBundle\Invis1bleSerializerExtensionBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new Invis1bleSerializerExtensionBundle();
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/tests/' . PHP_VERSION_ID . '/' . self::VERSION
            . '/' . $this->environment . '/' . (int) $this->debug;
    }

    public function process(ContainerBuilder $container): void
    {
        $normalizers = [];

        foreach ($container->findTaggedServiceIds('serializer.normalizer') as $id => $tags) {
            if (str_starts_with($id, 'invis1ble_serializer_extension.')) {
                $normalizers[$id] = $tags;
            }
        }

        $container->setParameter('test.bundle_normalizers', $normalizers);
    }

    protected function getConfigDir(): string
    {
        return $this->getCacheDir() . '/config';
    }

    private function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'serializer-extension-test',
            'test' => true,
            'serializer' => ['enabled' => true],
            'router' => ['utf8' => true],
        ]);

        $container->services()->set(UriController::class)->autowire()->public();
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('uri_round_trip', '/uri')->controller(UriController::class)->methods(['POST']);
    }
}
