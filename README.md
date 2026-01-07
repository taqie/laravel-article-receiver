# Laravel Article Receiver

Laravel package for receiving articles from external systems.

## PL

### Opis

Paczka udostepnia gotowe API do odbioru artykulow oraz powiazanych zasobow
(autorzy, kategorie, tagi, media). Zawiera walidacje, idempotency, limity
rate oraz punkty zaczepienia (hooks) do integracji z Twoja logika.

### Wymagania

- PHP 8.5+
- Laravel 11 lub 12
- Laravel Sanctum

### Instalacja

```bash
composer require taqie/laravel-article-receiver
```

### Konfiguracja

Publikacja configu:

```bash
php artisan vendor:publish --tag=article-receiver-config
```

Publikacja migracji (opcjonalnie):

```bash
php artisan vendor:publish --tag=article-receiver-migrations
```

### Hooki

Przyklady hookow przed/po create/update/delete:

```php
// config/article-receiver.php
'hooks' => [
    'before_create' => fn (ArticleData $data) => $data,
    'after_create' => fn (Article $article, ArticleData $data) => null,
    'before_update' => fn (Article $article, ArticleData $data) => $data,
    'after_update' => fn (Article $article, ArticleData $data) => null,
    'before_delete' => fn (Article $article) => null,
    'after_delete' => fn (int $articleId, array $payload) => null,
    'author' => [
        'before_create' => fn (AuthorData $data) => $data,
        'after_create' => fn (Author $author, AuthorData $data) => null,
    ],
    'category' => [
        'before_create' => fn (CategoryData $data) => $data,
        'after_create' => fn (Category $category, CategoryData $data) => null,
    ],
    'tag' => [
        'before_create' => fn (TagData $data) => $data,
        'after_create' => fn (Tag $tag, TagData $data) => null,
    ],
    'media' => [
        'before_create' => fn (UploadedFile $file, ?string $altText, ?int $articleId, ?string $folder) => null,
        'after_create' => fn (Media $media) => null,
    ],
],
```

Hooki moga byc closure, `Class@method` lub callable array.
`before_*` moze zwrocic zmodyfikowany DTO.

### Nadpisywanie zasobow (Resources)

```php
// config/article-receiver.php
'response' => [
    'resource' => null,
    'resources' => [
        'article' => \App\Http\Resources\ArticleResource::class,
        'author' => \App\Http\Resources\AuthorResource::class,
        'category' => \App\Http\Resources\CategoryResource::class,
        'tag' => \App\Http\Resources\TagResource::class,
        'media' => \App\Http\Resources\MediaResource::class,
    ],
],
```

### Mapowanie pol

```php
// config/article-receiver.php
'field_mapping' => [
    'title' => 'headline',
    'lead' => 'excerpt',
    'meta_description' => 'meta_desc',
    'body' => 'content',
    'author_id' => 'user_id',
    'category_id' => 'section_id',
    'featured_image_url' => 'featured_image',
    'published_at' => 'published_at',
    'metadata' => 'metadata',
],
```

Mapowanie jest stosowane przy create/update artykulu.

### Testy i pokrycie

Uruchomienie testow:

```bash
vendor/bin/pest
```

Pokrycie kodu (wymaga wsparcia Xdebug lub PCOV):

```bash
vendor/bin/pest --coverage
```

## EN

### Overview

Provides a ready-to-use API for receiving articles and related resources
(authors, categories, tags, media). Includes validation, idempotency,
rate limits, and hooks for integration with your own logic.

### Requirements

- PHP 8.5+
- Laravel 11 or 12
- Laravel Sanctum

### Installation

```bash
composer require taqie/laravel-article-receiver
```

### Configuration

Publish config:

```bash
php artisan vendor:publish --tag=article-receiver-config
```

Publish migrations (optional):

```bash
php artisan vendor:publish --tag=article-receiver-migrations
```

### Hooks

```php
// config/article-receiver.php
'hooks' => [
    'before_create' => fn (ArticleData $data) => $data,
    'after_create' => fn (Article $article, ArticleData $data) => null,
    'before_update' => fn (Article $article, ArticleData $data) => $data,
    'after_update' => fn (Article $article, ArticleData $data) => null,
    'before_delete' => fn (Article $article) => null,
    'after_delete' => fn (int $articleId, array $payload) => null,
    'author' => [
        'before_create' => fn (AuthorData $data) => $data,
        'after_create' => fn (Author $author, AuthorData $data) => null,
    ],
    'category' => [
        'before_create' => fn (CategoryData $data) => $data,
        'after_create' => fn (Category $category, CategoryData $data) => null,
    ],
    'tag' => [
        'before_create' => fn (TagData $data) => $data,
        'after_create' => fn (Tag $tag, TagData $data) => null,
    ],
    'media' => [
        'before_create' => fn (UploadedFile $file, ?string $altText, ?int $articleId, ?string $folder) => null,
        'after_create' => fn (Media $media) => null,
    ],
],
```

Hooks can be closures, `Class@method` strings, or callable arrays.
`before_*` hooks may return a modified DTO.

### Resource Overrides

```php
// config/article-receiver.php
'response' => [
    'resource' => null,
    'resources' => [
        'article' => \App\Http\Resources\ArticleResource::class,
        'author' => \App\Http\Resources\AuthorResource::class,
        'category' => \App\Http\Resources\CategoryResource::class,
        'tag' => \App\Http\Resources\TagResource::class,
        'media' => \App\Http\Resources\MediaResource::class,
    ],
],
```

### Field Mapping

```php
// config/article-receiver.php
'field_mapping' => [
    'title' => 'headline',
    'lead' => 'excerpt',
    'meta_description' => 'meta_desc',
    'body' => 'content',
    'author_id' => 'user_id',
    'category_id' => 'section_id',
    'featured_image_url' => 'featured_image',
    'published_at' => 'published_at',
    'metadata' => 'metadata',
],
```

Mapping is applied in the article create/update actions before saving.

### Testing and coverage

```bash
vendor/bin/pest
```

Coverage (requires Xdebug or PCOV):

```bash
vendor/bin/pest --coverage
```

## Status

This package is under active development. See `docs/PLAN-LARAVEL-PACKAGE-ARTICLE-RECEIVER.md` for the roadmap.

## Documentation

- Integration guide: `docs/INTEGRATION-GUIDE.md`
- API specification: `docs/TARGET-SITE-API-SPECIFICATION.md`
- OpenAPI (Markdown): `docs/OPENAPI.md`

## License

MIT.
