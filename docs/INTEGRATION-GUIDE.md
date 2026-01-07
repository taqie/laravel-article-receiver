# Integration Guide: Laravel Article Receiver

This guide explains how to integrate an external system with the Article Receiver API and how to prepare the target Laravel app for deployment.

## 1. Target App Setup

### 1.1 Requirements

- PHP 8.5+
- Laravel 11/12
- Laravel Sanctum

### 1.2 Install Package

```bash
composer require taqie/laravel-article-receiver
```

### 1.3 Publish Config and Migrations

```bash
php artisan vendor:publish --tag=article-receiver-config
php artisan vendor:publish --tag=article-receiver-migrations
php artisan migrate
```

### 1.4 Configure Sanctum

Ensure Sanctum is installed and the `auth:sanctum` middleware is enabled on the API routes.
The package uses `auth:sanctum` by default via `config/article-receiver.php`.

### 1.5 Generate API Token

```bash
php artisan article-receiver:token --user=1 --name=seo-pipeline --abilities=articles:create articles:update articles:delete media:upload
```

Store the token securely in your external system.

---

## 2. API Authentication

- Type: Bearer token (Sanctum)
- Header:

```
Authorization: Bearer {api_token}
Accept: application/json
```

---

## 3. Endpoints (Summary)

- `GET /api/health`
- `POST /api/articles`
- `PUT /api/articles/{id}`
- `GET /api/articles/{id}`
- `DELETE /api/articles/{id}`
- `POST /api/media`
- `DELETE /api/media/{id}`
- `GET /api/authors`
- `POST /api/authors`
- `GET /api/authors/{id}`
- `PUT /api/authors/{id}`
- `DELETE /api/authors/{id}`
- `GET /api/categories`
- `POST /api/categories`
- `GET /api/categories/{id}`
- `PUT /api/categories/{id}`
- `DELETE /api/categories/{id}`
- `GET /api/tags`
- `POST /api/tags`
- `GET /api/tags/{id}`
- `PUT /api/tags/{id}`
- `DELETE /api/tags/{id}`

Full payload examples and validation rules are in `docs/TARGET-SITE-API-SPECIFICATION.md`.

---

## 4. Payload Example (Create Article)

```json
{
  "title": "AI w praktyce",
  "lead": "Zajawka artykulu",
  "meta_description": "Opis SEO",
  "body": "<p>HTML body</p>",
  "tags": ["AI", "biznes"],
  "status": "draft",
  "author_id": 1,
  "category_id": 2,
  "featured_image_url": "https://example.com/image.jpg",
  "published_at": null,
  "metadata": {
    "source_ref": "https://source.com/article"
  }
}
```

---

## 5. Idempotency

To prevent duplicate article creation, use the `X-Idempotency-Key` header:

```
X-Idempotency-Key: {uuid}
```

If a duplicate request is detected, the API returns 409 with the cached response.

---

## 6. Media Upload

Send `multipart/form-data` with a single `file` field:

```bash
curl -X POST https://target-site.com/api/media \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "file=@/path/to/image.jpg" \
  -F "alt_text=Example"
```

---

## 7. Field Mapping

If your target app uses different column names, map API fields in config:

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

---

## 8. Response Customization

You can provide your own API Resources:

```php
// config/article-receiver.php
'response' => [
    'resources' => [
        'article' => \App\Http\Resources\ArticleResource::class,
        'author' => \App\Http\Resources\AuthorResource::class,
        'category' => \App\Http\Resources\CategoryResource::class,
        'tag' => \App\Http\Resources\TagResource::class,
        'media' => \App\Http\Resources\MediaResource::class,
    ],
],
```

---

## 9. Deployment Checklist

- [ ] Package installed and config published
- [ ] Migrations run
- [ ] Sanctum installed and `auth:sanctum` enabled
- [ ] API token generated and stored in external system
- [ ] Health endpoint responds 200
- [ ] Create/update/delete article works
- [ ] Media upload works
- [ ] Idempotency verified

