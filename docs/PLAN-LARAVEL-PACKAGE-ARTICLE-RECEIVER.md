# Plan: Laravel Package - Article Receiver

Paczka Laravel umożliwiająca łatwe przyjmowanie artykułów z SEO Content Pipeline (lub innych systemów).

---

## 1. Podstawowe informacje

### 1.1 Nazwa paczki
- **Composer:** `taqie/laravel-article-receiver`
- **Alternatywy:** `article-inbox`, `content-receiver`, `remote-publisher`

### 1.2 Cel
Zapewnić "plug & play" API do przyjmowania artykułów z zewnętrznych systemów:
- Zero-config dla podstawowego użycia
- Pełna konfigurowalność dla zaawansowanych przypadków
- Kompatybilność z istniejącymi modelami Article w aplikacji

### 1.3 Wymagania
- PHP 8.5+
- Laravel 11, 12
- Laravel Sanctum (jako peer dependency)

---

## 2. Funkcjonalności

### 2.1 Core Features
- ✅ Health check endpoint
- ✅ Articles CRUD API
- ✅ Media upload endpoint
- ✅ Idempotency (zapobieganie duplikatom)
- ✅ Walidacja request/response
- ✅ Rate limiting (konfigurowalne)

### 2.2 Elastyczność
- ✅ Własny model Article (lub domyślny z paczki)
- ✅ Własne mapowanie pól (field mapping)
- ✅ Hooki/Events (przed/po utworzeniu artykułu)
- ✅ Własna logika autoryzacji
- ✅ Customowe endpointy

### 2.3 Developer Experience
- ✅ Artisan commands (token generation, health check)
- ✅ Config publish
- ✅ Migration publish (opcjonalne)
- ✅ Testy do skopiowania

---

## 3. Struktura paczki

```
laravel-article-receiver/
├── src/
│   ├── ArticleReceiverServiceProvider.php
│   ├── Facades/
│   │   └── ArticleReceiver.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HealthController.php
│   │   │   ├── ArticleController.php
│   │   │   └── MediaController.php
│   │   ├── Requests/
│   │   │   ├── StoreArticleRequest.php
│   │   │   └── UpdateArticleRequest.php
│   │   ├── Resources/
│   │   │   └── ArticleResource.php
│   │   └── Middleware/
│   │       └── IdempotencyMiddleware.php
│   ├── Models/
│   │   ├── Article.php
│   │   ├── Author.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   └── Media.php
│   ├── Contracts/
│   │   └── ArticleModelInterface.php
│   ├── Events/
│   │   ├── ArticleCreating.php
│   │   ├── ArticleCreated.php
│   │   ├── ArticleUpdating.php
│   │   ├── ArticleUpdated.php
│   │   └── ArticleDeleted.php
│   ├── Services/
│   │   ├── ArticleService.php
│   │   └── MediaService.php
│   ├── Commands/
│   │   ├── GenerateTokenCommand.php
│   │   ├── HealthCheckCommand.php
│   │   └── InstallCommand.php
│   └── Traits/
│       └── ReceivesArticles.php
├── config/
│   └── article-receiver.php
├── database/
│   └── migrations/
│       ├── create_authors_table.php
│       ├── create_categories_table.php
│       ├── create_tags_table.php
│       ├── create_articles_table.php
│       ├── create_article_tag_table.php
│       └── create_media_table.php
├── routes/
│   └── api.php
├── tests/
│   ├── Feature/
│   │   ├── HealthEndpointTest.php
│   │   ├── ArticleCrudTest.php
│   │   └── MediaUploadTest.php
│   └── TestCase.php
├── stubs/
│   ├── article-model.stub
│   └── article-migration.stub
├── composer.json
├── README.md
├── LICENSE
└── CHANGELOG.md
```

---

## 4. Konfiguracja

### 4.1 Config file: `config/article-receiver.php`

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => true,
        'prefix' => 'api',
        'middleware' => ['api', 'auth:sanctum'],
        'rate_limit' => 60, // requests per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    | Specify your own models or use the default ones.
    | Set to null to disable a feature (e.g., tags, categories).
    */
    'models' => [
        'article'  => \Taqie\LaravelArticleReceiver\Models\Article::class,
        'author'   => \Taqie\LaravelArticleReceiver\Models\Author::class,
        'category' => \Taqie\LaravelArticleReceiver\Models\Category::class,
        'tag'      => \Taqie\LaravelArticleReceiver\Models\Tag::class,
        'media'    => \Taqie\LaravelArticleReceiver\Models\Media::class,

        // Przykład użycia własnych modeli:
        // 'article'  => \App\Models\Post::class,
        // 'author'   => \App\Models\Author::class,
        // 'category' => null, // wyłączone
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Mapping
    |--------------------------------------------------------------------------
    | Map incoming API fields to your model's database columns.
    | Format: 'api_field' => 'database_column'
    */
    'field_mapping' => [
        'title' => 'title',
        'lead' => 'lead',              // lub 'excerpt', 'summary'
        'meta_description' => 'meta_description',
        'body' => 'body',              // lub 'content', 'text'
        'status' => 'status',
        'author_id' => 'author_id',    // lub 'user_id'
        'category_id' => 'category_id',
        'featured_image_url' => 'featured_image',
        'published_at' => 'published_at',
        'metadata' => 'metadata',      // lub 'meta', 'extra'
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Mapping
    |--------------------------------------------------------------------------
    | Map API status values to your application's status values.
    */
    'status_mapping' => [
        'draft' => 'draft',
        'published' => 'published',
        // 'draft' => 0,
        // 'published' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'status' => 'draft',
        'author_id' => null, // lub ID domyślnego autora
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */
    'media' => [
        'enabled' => true,
        'disk' => 'public',
        'directory' => 'articles/{year}/{month}',
        'max_size' => 10240, // KB (10MB)
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Idempotency
    |--------------------------------------------------------------------------
    */
    'idempotency' => [
        'enabled' => true,
        'header' => 'X-Idempotency-Key',
        'ttl' => 86400, // 24 hours (seconds)
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generation
    |--------------------------------------------------------------------------
    */
    'url' => [
        'route_name' => 'articles.show', // named route dla artykułu
        // lub
        'pattern' => '/blog/{slug}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hooks / Callbacks
    |--------------------------------------------------------------------------
    */
    'hooks' => [
        'before_create' => null, // callable lub class@method
        'after_create' => null,
        'before_update' => null,
        'after_update' => null,
        'before_delete' => null,
        'after_delete' => null,
        'author' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'category' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'tag' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'media' => [
            'before_create' => null,
            'after_create' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules (override defaults)
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'store_article' => [],
        'update_article' => [],
        'store_author' => [],
        'update_author' => [],
        'store_category' => [],
        'update_category' => [],
        'store_tag' => [],
        'update_tag' => [],
        'upload_media' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Transformation
    |--------------------------------------------------------------------------
    */
    'response' => [
        'resource' => \YourVendor\ArticleReceiver\Http\Resources\ArticleResource::class,
        // lub własny: \App\Http\Resources\ArticleResource::class
        'resources' => [
            'article' => null,
            'author' => null,
            'category' => null,
            'tag' => null,
            'media' => null,
        ],
    ],
];
```

---

## 5. Modele

### 5.1 Article (core)

| Pole | Typ | Opis |
|------|-----|------|
| `id` | bigint | Primary key |
| `title` | string(255) | Tytuł artykułu |
| `slug` | string(255) | URL-friendly slug (unique) |
| `lead` | text | Lead/zajawka |
| `meta_description` | string(160) | SEO meta description |
| `body` | longText | Treść HTML |
| `status` | string | draft / published |
| `author_id` | foreignId | FK → authors |
| `category_id` | foreignId | FK → categories (nullable) |
| `featured_image_url` | string(500) | URL obrazka głównego |
| `published_at` | timestamp | Data publikacji |
| `metadata` | json | Dodatkowe dane |
| `idempotency_key` | string(64) | Klucz idempotentności (unique) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

### 5.2 Author

| Pole | Typ | Opis |
|------|-----|------|
| `id` | bigint | Primary key |
| `name` | string(255) | Imię i nazwisko |
| `email` | string(255) | Email (unique, nullable) |
| `bio` | text | Biografia (nullable) |
| `avatar_url` | string(500) | URL avatara (nullable) |
| `website` | string(255) | Strona WWW (nullable) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

### 5.3 Category

| Pole | Typ | Opis |
|------|-----|------|
| `id` | bigint | Primary key |
| `name` | string(255) | Nazwa kategorii |
| `slug` | string(255) | Slug (unique) |
| `description` | text | Opis (nullable) |
| `parent_id` | foreignId | FK → categories (nullable, self-reference) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

### 5.4 Tag

| Pole | Typ | Opis |
|------|-----|------|
| `id` | bigint | Primary key |
| `name` | string(100) | Nazwa tagu |
| `slug` | string(100) | Slug (unique) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

**Pivot table: `article_tag`**
| Pole | Typ |
|------|-----|
| `article_id` | foreignId |
| `tag_id` | foreignId |

### 5.5 Media

| Pole | Typ | Opis |
|------|-----|------|
| `id` | bigint | Primary key |
| `article_id` | foreignId | FK → articles (nullable) |
| `filename` | string(255) | Nazwa pliku |
| `path` | string(500) | Ścieżka w storage |
| `disk` | string(50) | Disk (public, s3, etc.) |
| `mime_type` | string(100) | Typ MIME |
| `size` | int | Rozmiar w bajtach |
| `alt_text` | string(255) | Tekst alternatywny (nullable) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

### 5.6 Relacje

```
Article
├── belongsTo Author
├── belongsTo Category (nullable)
├── belongsToMany Tag (pivot: article_tag)
└── hasMany Media

Author
└── hasMany Article

Category
├── hasMany Article
├── belongsTo Category (parent)
└── hasMany Category (children)

Tag
└── belongsToMany Article

Media
└── belongsTo Article
```

---

## 6. Użycie

### 6.1 Instalacja

```bash
composer require taqie/laravel-article-receiver

php artisan article-receiver:install
```

**Komenda install wykonuje:**
1. Publikuje config
2. Pyta czy użyć domyślnej migracji czy własny model
3. Uruchamia migracje (opcjonalnie)
4. Generuje pierwszy token API

### 6.2 Szybki start (domyślny model)

```bash
# Instalacja
composer require taqie/laravel-article-receiver

# Publikuj config i migracje
php artisan vendor:publish --tag=article-receiver-config
php artisan vendor:publish --tag=article-receiver-migrations

# Uruchom migracje
php artisan migrate

# Wygeneruj token
php artisan article-receiver:token
# Output: Your API token: 1|abc123...
```

Gotowe! API dostępne pod `/api/articles`.

### 6.3 Użycie własnego modelu

```php
// config/article-receiver.php
'article_model' => \App\Models\Post::class,

'field_mapping' => [
    'title' => 'title',
    'lead' => 'excerpt',        // twoja kolumna
    'body' => 'content',        // twoja kolumna
    'author_id' => 'user_id',   // twoja kolumna
    // ...
],
```

```php
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use YourVendor\ArticleReceiver\Traits\ReceivesArticles;

class Post extends Model
{
    use ReceivesArticles;

    // Twoja istniejąca logika...
}
```

### 6.4 Eventy / Hooki

```php
// app/Providers/EventServiceProvider.php
use YourVendor\ArticleReceiver\Events\ArticleCreated;

protected $listen = [
    ArticleCreated::class => [
        \App\Listeners\NotifyEditorAboutNewArticle::class,
        \App\Listeners\SendToSlack::class,
    ],
];
```

```php
// app/Listeners/NotifyEditorAboutNewArticle.php
namespace App\Listeners;

use YourVendor\ArticleReceiver\Events\ArticleCreated;

class NotifyEditorAboutNewArticle
{
    public function handle(ArticleCreated $event): void
    {
        $article = $event->article;
        $metadata = $event->metadata;

        // Wyślij email, Slack, etc.
    }
}
```

### 6.5 Customowa walidacja

```php
// config/article-receiver.php
'validation' => [
    'title' => ['required', 'string', 'max:200', 'unique:posts,title'],
    'body' => ['required', 'string', 'min:500'], // min 500 znaków
    'category_id' => ['required', 'exists:categories,id'],
],
```

### 6.6 Własny Resource

```php
// app/Http/Resources/MyArticleResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'url' => route('posts.show', $this->slug),
            'title' => $this->title,
            'status' => $this->status,
            'author' => $this->user->name, // własna logika
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

// config/article-receiver.php
'response' => [
    'resource' => \App\Http\Resources\MyArticleResource::class,
],
```

---

## 7. Artisan Commands

### 7.1 Install

```bash
php artisan article-receiver:install

# Interaktywny wizard:
# - Czy publikować migracje?
# - Czy użyć własnego modelu?
# - Czy wygenerować token?
```

### 7.2 Generate Token

```bash
# Dla domyślnego użytkownika
php artisan article-receiver:token

# Dla konkretnego użytkownika
php artisan article-receiver:token --user=1

# Z custom nazwą
php artisan article-receiver:token --name="SEO Pipeline Production"

# Z abilities
php artisan article-receiver:token --abilities=articles:create,articles:update
```

### 7.3 Health Check

```bash
# Sprawdź czy API działa
php artisan article-receiver:health

# Output:
# ✓ Routes registered
# ✓ Database connection OK
# ✓ Model configured: App\Models\Post
# ✓ Media disk writable
```

### 7.4 List Tokens

```bash
php artisan article-receiver:tokens

# Output:
# +----+----------------------+---------------------+
# | ID | Name                 | Last Used           |
# +----+----------------------+---------------------+
# | 1  | SEO Pipeline Prod    | 2025-01-15 12:00:00 |
# | 2  | SEO Pipeline Staging | Never               |
# +----+----------------------+---------------------+
```

---

## 8. Trait: ReceivesArticles

```php
<?php

namespace YourVendor\ArticleReceiver\Traits;

trait ReceivesArticles
{
    /**
     * Boot the trait.
     */
    public static function bootReceivesArticles(): void
    {
        static::creating(function ($model) {
            // Auto-generate slug if not present
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    /**
     * Get the URL for this article.
     */
    public function getArticleUrlAttribute(): string
    {
        $config = config('article-receiver.url');

        if (isset($config['route_name'])) {
            return route($config['route_name'], $this);
        }

        return url(str_replace('{slug}', $this->slug, $config['pattern']));
    }

    /**
     * Scope: Only articles from remote sources.
     */
    public function scopeFromRemote($query)
    {
        return $query->whereNotNull('idempotency_key');
    }

    /**
     * Check if article was created via API.
     */
    public function isFromRemote(): bool
    {
        return !empty($this->idempotency_key);
    }
}
```

---

## 9. Contract: ArticleModelInterface

```php
<?php

namespace YourVendor\ArticleReceiver\Contracts;

interface ArticleModelInterface
{
    /**
     * Get the URL for this article.
     */
    public function getArticleUrlAttribute(): string;

    /**
     * Get the fillable fields for mass assignment.
     */
    public function getFillable(): array;
}
```

---

## 10. Middleware: Idempotency

```php
<?php

namespace YourVendor\ArticleReceiver\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('article-receiver.idempotency.enabled')) {
            return $next($request);
        }

        $key = $request->header(config('article-receiver.idempotency.header'));

        if (!$key) {
            return $next($request);
        }

        $cacheKey = "idempotency:{$key}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json([
                'message' => 'Request already processed.',
                ...$cached,
            ], 409);
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            Cache::put(
                $cacheKey,
                $response->getData(true),
                config('article-receiver.idempotency.ttl')
            );
        }

        return $response;
    }
}
```

---

## 11. Testy

### 11.1 Feature Tests (do skopiowania przez użytkownika)

```bash
php artisan vendor:publish --tag=article-receiver-tests
```

```php
// tests/Feature/ArticleReceiverTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleReceiverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_health_endpoint_works(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/health');

        $response->assertOk()
            ->assertJsonStructure(['status', 'timestamp']);
    }

    public function test_can_create_article(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/articles', [
                'title' => 'Test Article',
                'lead' => 'Test lead text',
                'meta_description' => 'Test meta description',
                'body' => '<p>Test body</p>',
            ]);

        $response->assertCreated()
            ->assertJsonStructure(['id', 'url', 'status']);
    }

    public function test_idempotency_prevents_duplicates(): void
    {
        $idempotencyKey = 'test-key-123';

        $first = $this->withToken($this->token)
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/articles', [
                'title' => 'Test',
                'lead' => 'Lead',
                'meta_description' => 'Meta',
                'body' => 'Body',
            ]);

        $first->assertCreated();

        $second = $this->withToken($this->token)
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/articles', [
                'title' => 'Test 2',
                'lead' => 'Lead 2',
                'meta_description' => 'Meta 2',
                'body' => 'Body 2',
            ]);

        $second->assertStatus(409);
    }

    // ... więcej testów
}
```

---

## 12. README.md (draft)

```markdown
# Laravel Article Receiver

Easily receive articles from remote systems via REST API.

## Installation

composer require taqie/laravel-article-receiver

php artisan article-receiver:install

## Quick Start

# Generate API token
php artisan article-receiver:token

# That's it! Your API is ready at /api/articles

## Using Your Own Model

// config/article-receiver.php
'article_model' => \App\Models\Post::class,

// Add trait to your model
use YourVendor\ArticleReceiver\Traits\ReceivesArticles;

class Post extends Model
{
    use ReceivesArticles;
}

## Events

Listen to ArticleCreated, ArticleUpdated, ArticleDeleted events.

## Documentation

Full documentation at: https://...

## License

MIT
```

---

## 13. Fazy implementacji

### Faza 1: Core (MVP)
- [ ] Service Provider
- [ ] Config file
- [ ] Models (Article, Author, Category, Tag, Media)
- [ ] Migrations (wszystkie tabele + pivot)
- [ ] Routes (health, articles CRUD)
- [ ] Controllers (Health, Article, Author, Category, Tag, Media)
- [ ] Requests (validation)
- [ ] Resources (JSON responses)
- [ ] Basic tests

### Faza 2: Elastyczność
- [ ] Field mapping
- [ ] Custom model support (config models)
- [ ] Trait ReceivesArticles
- [ ] Events (Created, Updated, Deleted)
- [ ] Hooks in config
- [ ] Możliwość wyłączenia modeli (null w config)

### Faza 3: Features
- [ ] Media upload (z obsługą różnych disków)
- [ ] Idempotency middleware
- [ ] Rate limiting
- [ ] Artisan commands (install, token, health, tokens)

### Faza 4: Polish
- [ ] Install wizard (interaktywny)
- [ ] Health check command
- [ ] Publishable tests
- [ ] README.md
- [ ] CHANGELOG.md
- [ ] LICENSE
- [ ] GitHub Actions (CI)

---

## 14. Publikacja

### 14.1 Packagist
1. Utwórz repo na GitHub
2. Zarejestruj na packagist.org
3. Setup webhook dla auto-update

### 14.2 Dokumentacja
- README.md (quick start)
- GitHub Wiki lub docs folder
- Opcjonalnie: dedykowana strona (VitePress/Docusaurus)

### 14.3 Versioning
- Semantic versioning (1.0.0, 1.1.0, 2.0.0)
- CHANGELOG.md
- Git tags

---

## 15. Podsumowanie

**Korzyści z paczki:**
1. **Szybkość wdrożenia:** 5 minut zamiast kilku godzin
2. **Standaryzacja:** Jednolite API we wszystkich target sites
3. **Utrzymanie:** Aktualizacje w jednym miejscu
4. **Testowanie:** Gotowe testy do skopiowania
5. **Elastyczność:** Działa z istniejącymi modelami

**Następne kroki:**
1. Zatwierdzenie planu
2. Utworzenie repozytorium
3. Implementacja Fazy 1 (Core)
4. Testowanie na 1-2 target sites
5. Publikacja na Packagist
