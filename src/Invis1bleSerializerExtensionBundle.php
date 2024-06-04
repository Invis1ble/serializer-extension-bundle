<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Invis1bleSerializerExtensionBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.xml');

        if ('serializer_test' === $container->env()) {
            $container->import('../config/services_serializer_test.xml');
        }
    }
}
