<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\HttpFactory;
use Invis1ble\SymfonySerializerExtension\Normalizer\UriNormalizer;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('invis1ble_serializer_extension.uri_factory', HttpFactory::class)
        ->public();

    $services->alias(UriFactoryInterface::class, 'invis1ble_serializer_extension.uri_factory');

    $services->set('invis1ble_serializer_extension.normalizer.uri', UriNormalizer::class)
        ->autowire()
        ->args([service('invis1ble_serializer_extension.uri_factory')])
        ->tag('serializer.normalizer', ['priority' => 10]);
};
