<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

return function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('serializer.normalizer.property', PropertyNormalizer::class)
        ->autowire()
        ->autoconfigure()
        ->tag('serializer.normalizer');
};
