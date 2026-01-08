# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Initial release preparation.

## [0.1.2] - 2026-01-08

- Add configurable table prefix for all package tables.
- Update migrations, models, relations, and validation rules to use prefixed tables.
- Document table prefix configuration in the README.

## [0.1.1] - 2026-01-07

- README updated with PL/EN instructions and Packagist badges.
- Internal implementation plans removed from release.

## [0.1.0] - 2026-01-07

- Initial release of the article receiver package.
- REST API for articles, authors, categories, tags, and media.
- Hooks, DTOs, and events for customization points.
- Idempotency middleware and rate limiting.
- Media uploads with validation and storage.
- Artisan commands for setup and token generation.
