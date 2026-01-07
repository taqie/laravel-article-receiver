# Target Site API Specification

Specyfikacja API dla aplikacji Laravel, które mają przyjmować artykuły z SEO Content Pipeline.

---

## 1. Wymagania

### 1.1 Techniczne
- PHP 8.5+
- Laravel 11/12
- Laravel Sanctum do autoryzacji
- Obsługa JSON API
- Obsługa multipart/form-data (upload media)

### 1.2 Autoryzacja
- **Typ:** Bearer Token (Laravel Sanctum)
- **Header:** `Authorization: Bearer {api_token}`
- **Token abilities (opcjonalnie):** `articles:create`, `articles:update`, `articles:delete`, `media:upload`

---

## 2. Endpoints

### 2.1 Health Check

```
GET /api/health
```

**Cel:** Weryfikacja połączenia i dostępności API.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response 200:**
```json
{
    "status": "ok",
    "timestamp": "2025-01-15T12:00:00Z",
    "version": "1.0.0"
}
```

**Response 401:**
```json
{
    "message": "Unauthenticated."
}
```

---

### 2.2 Create Article

```
POST /api/articles
```

**Cel:** Utworzenie nowego artykułu.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
X-Idempotency-Key: {uuid}  // opcjonalne, zapobiega duplikatom
```

**Request Body:**
```json
{
    "title": "Jak AI zmienia biznes w 2025",
    "lead": "Sztuczna inteligencja rewolucjonizuje sposób prowadzenia firm. Poznaj najważniejsze trendy.",
    "meta_description": "Przewodnik po AI w biznesie 2025. Dowiedz się jak wdrożyć sztuczną inteligencję w swojej firmie.",
    "body": "<h2>Wprowadzenie</h2><p>Sztuczna inteligencja...</p><h2>Trendy AI</h2><p>W 2025 roku...</p>",
    "tags": ["AI", "biznes", "technologia", "2025"],
    "status": "draft",
    "author_id": 1,
    "category_id": 5,
    "featured_image_url": "https://target-site.com/storage/images/abc123.jpg",
    "published_at": null,
    "metadata": {
        "source_ref": "https://original-source.com/article",
        "generated_by": "seo-pipeline",
        "keyword": "AI w biznesie"
    }
}
```

**Pola:**

| Pole | Typ | Wymagane | Opis |
|------|-----|----------|------|
| `title` | string | ✅ | Tytuł artykułu (max 255) |
| `lead` | string | ✅ | Lead/zajawka (max 500) |
| `meta_description` | string | ✅ | SEO meta description (max 160) |
| `body` | string | ✅ | Treść HTML artykułu |
| `tags` | array | ❌ | Lista tagów (strings), zapisywane relacją many-to-many |
| `status` | string | ❌ | `draft` / `published` (default: `draft`) |
| `author_id` | int | ❌ | ID autora (model Author) |
| `category_id` | int | ❌ | ID kategorii (model Category) |
| `featured_image_url` | string | ❌ | URL obrazka głównego (po uprzednim uploadzie) |
| `published_at` | datetime | ❌ | Data publikacji (ISO 8601) |
| `metadata` | object | ❌ | Dodatkowe dane (dowolna struktura) |

**Response 201 Created:**
```json
{
    "id": "123",
    "url": "https://target-site.com/blog/jak-ai-zmienia-biznes-w-2025",
    "status": "draft",
    "created_at": "2025-01-15T12:00:00Z"
}
```

**Response 422 Validation Error:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "title": ["The title field is required."],
        "body": ["The body field is required."]
    }
}
```

**Response 409 Conflict (duplikat z Idempotency-Key):**
```json
{
    "message": "Article already exists.",
    "id": "123",
    "url": "https://target-site.com/blog/jak-ai-zmienia-biznes-w-2025"
}
```

---

### 2.3 Update Article

```
PUT /api/articles/{id}
```

**Cel:** Aktualizacja istniejącego artykułu.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Jak AI zmienia biznes w 2025 - Aktualizacja",
    "body": "<h2>Wprowadzenie</h2><p>Zaktualizowana treść...</p>",
    "status": "published",
    "published_at": "2025-01-15T14:00:00Z"
}
```

> **Uwaga:** Tylko przekazane pola są aktualizowane (PATCH semantics).

**Response 200:**
```json
{
    "id": "123",
    "url": "https://target-site.com/blog/jak-ai-zmienia-biznes-w-2025-aktualizacja",
    "status": "published",
    "updated_at": "2025-01-15T14:00:00Z"
}
```

**Response 404:**
```json
{
    "message": "Article not found."
}
```

---

### 2.4 Get Article

```
GET /api/articles/{id}
```

**Cel:** Pobranie szczegółów artykułu.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response 200:**
```json
{
    "id": "123",
    "title": "Jak AI zmienia biznes w 2025",
    "lead": "Sztuczna inteligencja rewolucjonizuje...",
    "meta_description": "Przewodnik po AI w biznesie 2025...",
    "body": "<h2>Wprowadzenie</h2><p>...</p>",
    "tags": ["AI", "biznes"],
    "status": "published",
    "url": "https://target-site.com/blog/jak-ai-zmienia-biznes-w-2025",
    "author": {
        "id": 1,
        "name": "Jan Kowalski"
    },
    "category": {
        "id": 5,
        "name": "Technologia"
    },
    "featured_image_url": "https://target-site.com/storage/images/abc123.jpg",
    "published_at": "2025-01-15T14:00:00Z",
    "created_at": "2025-01-15T12:00:00Z",
    "updated_at": "2025-01-15T14:00:00Z",
    "metadata": {
        "source_ref": "https://original-source.com/article"
    }
}
```

---

### 2.5 Delete Article

```
DELETE /api/articles/{id}
```

**Cel:** Usunięcie artykułu.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response 204 No Content:**
(puste body)

**Response 404:**
```json
{
    "message": "Article not found."
}
```

---

### 2.6 Upload Media

```
POST /api/media
```

**Cel:** Upload obrazka/pliku przed utworzeniem artykułu.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: multipart/form-data
```

**Request Body (multipart/form-data):**
```
file: (binary)
alt_text: "Opis obrazka dla SEO"
folder: "articles/2025/01"  // opcjonalne
```

**Limity:**
- Max file size: 10MB
- Allowed types: jpg, jpeg, png, gif, webp, svg

**Response 201:**
```json
{
    "id": "456",
    "url": "https://target-site.com/storage/images/abc123.jpg",
    "filename": "abc123.jpg",
    "mime_type": "image/jpeg",
    "size": 245678,
    "alt_text": "Opis obrazka dla SEO",
    "created_at": "2025-01-15T12:00:00Z"
}
```

**Response 422:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "file": ["The file must be an image.", "The file may not be greater than 10240 kilobytes."]
    }
}
```

---

### 2.7 Authors CRUD

```
GET    /api/authors          # Lista autorów
POST   /api/authors          # Utwórz autora
GET    /api/authors/{id}     # Pobierz autora
PUT    /api/authors/{id}     # Aktualizuj autora
DELETE /api/authors/{id}     # Usuń autora
```

**Cel:** Zarządzanie autorami artykułów.

**Request Body (POST/PUT):**
```json
{
    "name": "Jan Kowalski",
    "email": "jan@example.com",
    "bio": "Ekspert SEO z 10-letnim doświadczeniem.",
    "avatar_url": "https://example.com/avatar.jpg",
    "website": "https://jankowalski.pl"
}
```

**Pola:**

| Pole | Typ | Wymagane | Opis |
|------|-----|----------|------|
| `name` | string | ✅ | Imię i nazwisko (max 255) |
| `email` | string | ❌ | Email (unique, max 255) |
| `bio` | string | ❌ | Biografia |
| `avatar_url` | string | ❌ | URL avatara (max 500) |
| `website` | string | ❌ | Strona WWW (max 255) |

**Response 200 (GET lista):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Jan Kowalski",
            "email": "jan@example.com",
            "bio": "Ekspert SEO...",
            "avatar_url": "https://example.com/avatar.jpg",
            "website": "https://jankowalski.pl",
            "articles_count": 15
        }
    ]
}
```

---

### 2.8 Categories CRUD

```
GET    /api/categories          # Lista kategorii
POST   /api/categories          # Utwórz kategorię
GET    /api/categories/{id}     # Pobierz kategorię
PUT    /api/categories/{id}     # Aktualizuj kategorię
DELETE /api/categories/{id}     # Usuń kategorię
```

**Cel:** Zarządzanie kategoriami artykułów.

**Request Body (POST/PUT):**
```json
{
    "name": "Technologia",
    "slug": "technologia",
    "description": "Artykuły o nowych technologiach",
    "parent_id": null
}
```

**Pola:**

| Pole | Typ | Wymagane | Opis |
|------|-----|----------|------|
| `name` | string | ✅ | Nazwa kategorii (max 255) |
| `slug` | string | ❌ | Slug URL (auto-generowany jeśli brak) |
| `description` | string | ❌ | Opis kategorii |
| `parent_id` | int | ❌ | ID kategorii nadrzędnej |

**Response 200 (GET lista):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Aktualności",
            "slug": "aktualnosci",
            "description": null,
            "parent_id": null,
            "articles_count": 25
        },
        {
            "id": 5,
            "name": "Technologia",
            "slug": "technologia",
            "description": "Artykuły o nowych technologiach",
            "parent_id": null,
            "articles_count": 12
        }
    ]
}
```

---

### 2.9 Tags CRUD

```
GET    /api/tags          # Lista tagów
POST   /api/tags          # Utwórz tag
GET    /api/tags/{id}     # Pobierz tag
PUT    /api/tags/{id}     # Aktualizuj tag
DELETE /api/tags/{id}     # Usuń tag
```

**Cel:** Zarządzanie tagami artykułów.

**Request Body (POST/PUT):**
```json
{
    "name": "AI",
    "slug": "ai"
}
```

**Pola:**

| Pole | Typ | Wymagane | Opis |
|------|-----|----------|------|
| `name` | string | ✅ | Nazwa tagu (max 100) |
| `slug` | string | ❌ | Slug URL (auto-generowany jeśli brak) |

**Response 200 (GET lista):**
```json
{
    "data": [
        {"id": 1, "name": "AI", "slug": "ai", "articles_count": 8},
        {"id": 2, "name": "biznes", "slug": "biznes", "articles_count": 15},
        {"id": 3, "name": "SEO", "slug": "seo", "articles_count": 22}
    ]
}
```

---

## 3. Kody błędów HTTP

| Kod | Znaczenie | Kiedy |
|-----|-----------|-------|
| 200 | OK | Sukces (GET, PUT) |
| 201 | Created | Sukces (POST) |
| 204 | No Content | Sukces (DELETE) |
| 400 | Bad Request | Nieprawidłowy format żądania |
| 401 | Unauthorized | Brak lub nieprawidłowy token |
| 403 | Forbidden | Brak uprawnień do operacji |
| 404 | Not Found | Zasób nie istnieje |
| 409 | Conflict | Duplikat (idempotency) |
| 422 | Unprocessable Entity | Błąd walidacji |
| 429 | Too Many Requests | Rate limiting |
| 500 | Internal Server Error | Błąd serwera |
| 503 | Service Unavailable | Serwis niedostępny |

---

## 4. Rate Limiting

**Zalecane limity:**
- 60 requests/minute per token
- 1000 requests/hour per token

**Headers w odpowiedzi:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1705320000
```

**Response 429:**
```json
{
    "message": "Too Many Requests",
    "retry_after": 30
}
```

---

## 5. Przykładowa implementacja (Laravel)

### 5.1 Routes

```php
// routes/api.php
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\MediaController;

Route::middleware('auth:sanctum')->group(function () {
    // Health check
    Route::get('/health', fn() => response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0',
    ]));

    // Articles CRUD
    Route::apiResource('articles', ArticleController::class);

    // Authors CRUD
    Route::apiResource('authors', AuthorController::class);

    // Categories CRUD
    Route::apiResource('categories', CategoryController::class);

    // Tags CRUD
    Route::apiResource('tags', TagController::class);

    // Media upload
    Route::post('/media', [MediaController::class, 'store']);
});
```

### 5.2 Article Controller

```php
// app/Http/Controllers/Api/ArticleController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function store(StoreArticleRequest $request)
    {
        // Idempotency check
        $idempotencyKey = $request->header('X-Idempotency-Key');
        if ($idempotencyKey) {
            $existing = Article::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return response()->json([
                    'message' => 'Article already exists.',
                    'id' => (string) $existing->id,
                    'url' => $existing->url,
                ], 409);
            }
        }

        $article = Article::create([
            ...$request->validated(),
            'idempotency_key' => $idempotencyKey,
            'slug' => Str::slug($request->title),
        ]);

        return response()->json([
            'id' => (string) $article->id,
            'url' => $article->url,
            'status' => $article->status,
            'created_at' => $article->created_at->toIso8601String(),
        ], 201);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->update($request->validated());

        // Regenerate slug if title changed
        if ($request->has('title')) {
            $article->update(['slug' => Str::slug($request->title)]);
        }

        return response()->json([
            'id' => (string) $article->id,
            'url' => $article->url,
            'status' => $article->status,
            'updated_at' => $article->updated_at->toIso8601String(),
        ]);
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->noContent();
    }
}
```

### 5.3 Store Article Request

```php
// app/Http/Requests/StoreArticleRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sanctum handles auth
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'lead' => ['required', 'string', 'max:500'],
            'meta_description' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'status' => ['nullable', 'in:draft,published'],
            'author_id' => ['nullable', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'featured_image_url' => ['nullable', 'url'],
            'published_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
```

### 5.4 Article Model

```php
// app/Models/Article.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'lead',
        'meta_description',
        'body',
        'status',
        'author_id',
        'category_id',
        'featured_image_url',
        'published_at',
        'metadata',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => url("/blog/{$this->slug}"));
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
```

### 5.5 Article Migration

```php
// database/migrations/xxxx_create_articles_table.php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('lead');
    $table->string('meta_description', 160);
    $table->longText('body');
    $table->string('status')->default('draft');
    $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('featured_image_url', 500)->nullable();
    $table->timestamp('published_at')->nullable();
    $table->json('metadata')->nullable();
    $table->string('idempotency_key', 64)->nullable()->unique();
    $table->timestamps();

    $table->index('status');
    $table->index('published_at');
});
```

### 5.6 Media Controller

```php
// app/Http/Controllers/Api/MediaController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:10240'], // 10MB
            'alt_text' => ['nullable', 'string', 'max:255'],
            'folder' => ['nullable', 'string', 'max:100'],
        ]);

        $folder = $request->input('folder', 'articles/' . now()->format('Y/m'));
        $path = $request->file('file')->store($folder, 'public');

        return response()->json([
            'id' => uniqid(),
            'url' => Storage::disk('public')->url($path),
            'filename' => basename($path),
            'mime_type' => $request->file('file')->getMimeType(),
            'size' => $request->file('file')->getSize(),
            'alt_text' => $request->input('alt_text'),
            'created_at' => now()->toIso8601String(),
        ], 201);
    }
}
```

### 5.8 Field Mapping (Optional)

If your target site uses different column names, map incoming fields:

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

### 5.7 Sanctum Token Creation

```php
// Tworzenie tokena dla SEO Pipeline
$user = User::find(1); // lub dedykowany "API User"

$token = $user->createToken('seo-pipeline', [
    'articles:create',
    'articles:update',
    'articles:delete',
    'media:upload',
])->plainTextToken;

// Token do użycia w SEO Pipeline: $token
```

---

## 6. Testowanie API

### 6.1 cURL Examples

**Health Check:**
```bash
curl -X GET https://target-site.com/api/health \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Create Article:**
```bash
curl -X POST https://target-site.com/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Idempotency-Key: $(uuidgen)" \
  -d '{
    "title": "Test Article",
    "lead": "This is a test lead.",
    "meta_description": "Test meta description for SEO.",
    "body": "<p>Test body content.</p>",
    "status": "draft"
  }'
```

**Upload Media:**
```bash
curl -X POST https://target-site.com/api/media \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "file=@/path/to/image.jpg" \
  -F "alt_text=Test image"
```

### 6.2 Postman Collection

Zalecane utworzenie kolekcji Postman z:
- Environment variables: `base_url`, `api_token`
- Pre-request script dla Idempotency-Key
- Tests dla walidacji response

---

## 7. Checklist wdrożenia

### Target Site (aplikacja przyjmująca):

- [ ] Laravel Sanctum zainstalowany i skonfigurowany
- [ ] Migracje utworzone (authors, categories, tags, articles, article_tag, media)
- [ ] Modele z odpowiednimi relacjami:
  - [ ] `Article` (belongsTo Author, Category; belongsToMany Tag; hasMany Media)
  - [ ] `Author` (hasMany Article)
  - [ ] `Category` (hasMany Article; parent/children self-reference)
  - [ ] `Tag` (belongsToMany Article)
  - [ ] `Media` (belongsTo Article)
- [ ] Controllery z CRUD:
  - [ ] `ArticleController`
  - [ ] `AuthorController`
  - [ ] `CategoryController`
  - [ ] `TagController`
  - [ ] `MediaController`
- [ ] Form Requests z walidacją
- [ ] Routes w `api.php`
- [ ] Rate limiting skonfigurowane
- [ ] CORS skonfigurowane (jeśli potrzebne)
- [ ] Token API utworzony
- [ ] Testy API (Feature tests)

### SEO Pipeline (aplikacja wysyłająca):

- [ ] Token API zapisany w `target_sites.api_token`
- [ ] `LaravelApiDriver` zaimplementowany
- [ ] Test connection działa
- [ ] Publish article działa
- [ ] Media upload działa
- [ ] Idempotency key generowany
- [ ] Retry logic zaimplementowane

---

## 8. Wersjonowanie API

**Aktualna wersja:** 1.0.0

**Przyszłe wersje:**
- URL prefix: `/api/v2/articles`
- Lub header: `Accept: application/vnd.targetsite.v2+json`

**Changelog:**
- v1.0.0 - Initial release
