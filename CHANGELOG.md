# Changelog

## [1.1.0] - 2026-09-29

### Added

- Symfony 8 support using `invis1ble/symfony-serializer-extension` 1.2 or later.
- Container and HTTP integration tests for normalizer registration, autowiring,
  priority, URI and JSON round trips, and invalid URI input.
- Compatibility checks for PHP 8.1 / Symfony 6.4, PHP 8.2 / Symfony 7.4,
  PHP 8.4 / Symfony 8.0, and PHP 8.5 / Symfony 8.1.

### Changed

- Load PHP service definitions because Symfony 8 no longer supports XML DI
  configuration. Service IDs, the URI factory alias, and normalizer priority
  remain unchanged. Legacy XML files remain available for older applications
  that import them directly.
- Use stable development dependencies and an updated Docker test environment.

PHP 8.1 and Symfony 6.4 / 7.x remain supported. Symfony 8 requires PHP 8.4.1 or
later. Existing `^1.0` Composer constraints accept this release.

[1.1.0]: https://github.com/Invis1ble/serializer-extension-bundle/compare/v1.0.6...v1.1.0
