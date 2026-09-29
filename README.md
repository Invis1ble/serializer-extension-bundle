SerializerExtensionBundle
==========================

![CI Status](https://github.com/Invis1ble/serializer-extension-bundle/actions/workflows/ci.yml/badge.svg?event=push)
[![Code Coverage](https://codecov.io/gh/Invis1ble/serializer-extension-bundle/graph/badge.svg?token=SEBR5ZUYWL)](https://codecov.io/gh/Invis1ble/serializer-extension-bundle)
[![Packagist](https://img.shields.io/packagist/v/Invis1ble/serializer-extension-bundle.svg)](https://packagist.org/packages/Invis1ble/serializer-extension-bundle)
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)

The `SerializerExtensionBundle` integrates [invis1ble/symfony-serializer-extension](https://github.com/Invis1ble/symfony-serializer-extension) into the Symfony framework.

Requirements
------------

- PHP 8.1 or later.
- Symfony 6.4, 7.x, or 8.x. Symfony 8 requires PHP 8.4.1 or later.
- `invis1ble/symfony-serializer-extension` 1.2 or later in the 1.x release line.

Version 1.1 adds Symfony 8 support while preserving the existing service IDs,
URI factory alias, and normalizer priority. Existing `^1.0` Composer constraints
accept this release. To explicitly select it:

```sh
composer require invis1ble/serializer-extension-bundle:^1.1 --with-all-dependencies
```


Installation
------------

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require invis1ble/serializer-extension-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require invis1ble/serializer-extension-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Invis1ble\SerializerExtensionBundle\Invis1bleSerializerExtensionBundle::class => ['all' => true],
];
```

Usage
-----

Enable the Symfony serializer in your application:

```yaml
# config/packages/framework.yaml
framework:
    serializer: true
```

Inject `Symfony\Component\Serializer\SerializerInterface` and use the `json`
format to serialize a PSR-7 `UriInterface` to a JSON string or deserialize one to
`Psr\Http\Message\UriInterface` or `GuzzleHttp\Psr7\Uri`.

The bundle registers one `serializer.normalizer` service,
`invis1ble_serializer_extension.normalizer.uri`, with priority `10`, ahead of
Symfony's object and property normalizers. The public service
`invis1ble_serializer_extension.uri_factory` uses `GuzzleHttp\Psr7\HttpFactory`
and is aliased to `Psr\Http\Message\UriFactoryInterface` for autowiring.

Service definitions now load from `config/services.php`, because Symfony 8 no
longer supports XML service configuration. Applications that register the bundle
need no configuration changes. The legacy XML files remain available for older
Symfony applications that imported them directly; such imports must use the PHP
files when upgrading to Symfony 8.


Development
-----------

### Getting started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d --wait` to start the Docker containers
4. Run `docker compose exec -T php composer install` to install dependencies
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

The development image defaults to PHP 8.4. Set `PHP_VERSION` before building and
starting Compose to select another version. CI checks PHP 8.1 / Symfony 6.4,
PHP 8.2 / Symfony 7.4, PHP 8.4 / Symfony 8.0, and PHP 8.5 / Symfony 8.1 with stable
dependencies and real platform requirements.

Validate the package and installed platform requirements with:

```sh
docker compose exec -T php composer validate --strict
docker compose exec -T php composer check-platform-reqs
```

### Check for Coding Standards violations

Run PHP_CodeSniffer checks:

```sh
docker compose exec -T php bin/php_codesniffer
```

Run PHP-CS-Fixer checks:

```sh
docker compose exec -T php bin/php-cs-fixer
```

Run Rector checks:

```sh
docker compose exec -T php bin/rector
```


Testing
-------

The tests compile a minimal Symfony kernel, check service tags and autowiring,
and exercise URI round trips, invalid data, normalizer ordering, and an HTTP
request through the container serializer. To run them during development:

```sh
docker compose exec -T php vendor/bin/phpunit
```

To run with coverage

```sh
XDEBUG_MODE=coverage docker compose up -d --wait
docker compose exec -T php vendor/bin/phpunit --coverage-clover var/log/coverage-clover.xml
```


License
-------

[The MIT License](./LICENSE)
