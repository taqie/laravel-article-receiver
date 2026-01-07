# EPIC 04: Advanced Features & Polish

Funkcje zaawansowane, middleware, narzędzia deweloperskie i testy.

## Zadania

### [TASK-04.1] Idempotency Middleware
Utworzyć w `src/Http/Middleware/`:

- [ ] `IdempotencyMiddleware`:
    - Sprawdzenie nagłówka `X-Idempotency-Key` (konfigurowalny)
    - Wykorzystanie `IdempotencyService`
    - Dla duplikatów: zwracanie 409 Conflict z poprzednią odpowiedzią
    - Cache'owanie tylko udanych odpowiedzi (2xx)
    - Konfigurowalny TTL
    - Rejestracja middleware w ServiceProviderze

### [TASK-04.2] Rate Limiting
Skonfigurować w ServiceProviderze:

- [ ] Rate Limiter dla API:
    - Konfigurowalny limit z `config('article-receiver.routes.rate_limit')`
    - Limiter per token (Sanctum)
    - Nagłówki w odpowiedzi: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
    - Response 429 Too Many Requests

### [TASK-04.3] Artisan Commands
Utworzyć w `src/Console/Commands/`:

- [ ] `InstallCommand` (`article-receiver:install`):
    - Interaktywny wizard
    - Publikacja configu
    - Pytanie o publikację migracji
    - Pytanie o uruchomienie migracji
    - Generowanie pierwszego tokena API
- [ ] `GenerateTokenCommand` (`article-receiver:token`):
    - Opcje: `--user=`, `--name=`, `--abilities=`
    - Tworzenie Sanctum tokena
    - Wyświetlenie tokena (plain text)
- [ ] `HealthCheckCommand` (`article-receiver:health`):
    - Sprawdzenie: routes registered, database connection, model configured, media disk writable
    - Kolorowy output (✓/✗)
- [ ] `ListTokensCommand` (`article-receiver:tokens`):
    - Lista wszystkich tokenów API
    - Tabela: ID, Name, Last Used, Created At
    - Opcja `--revoke=ID` do unieważnienia

### [TASK-04.4] Events
Utworzyć w `src/Events/`:

- [ ] `ArticleCreating`:
    - Przed zapisem artykułu
    - Właściwości: `ArticleData $data`
    - Możliwość modyfikacji danych
- [ ] `ArticleCreated`:
    - Po zapisie artykułu
    - Właściwości: `Article $article`, `ArticleData $data`
- [ ] `ArticleUpdating`:
    - Przed aktualizacją
    - Właściwości: `Article $article`, `ArticleData $data`
- [ ] `ArticleUpdated`:
    - Po aktualizacji
    - Właściwości: `Article $article`, `ArticleData $data`
- [ ] `ArticleDeleted`:
    - Po usunięciu
    - Właściwości: `int $articleId`, `array $articleData`
- [ ] `MediaUploaded`:
    - Po uploadzie
    - Właściwości: `Media $media`

### [TASK-04.5] Publishable Assets
Skonfigurować w ServiceProviderze:

- [ ] Tag `article-receiver-config`:
    - `config/article-receiver.php`
- [ ] Tag `article-receiver-migrations`:
    - Wszystkie migracje
- [ ] Tag `article-receiver-tests`:
    - Przykładowe testy do skopiowania przez użytkownika

### [TASK-04.6] Feature Tests
Utworzyć w `tests/Feature/`:

- [ ] `HealthEndpointTest`:
    - Test 200 dla authenticated
    - Test 401 dla unauthenticated
    - Test struktury odpowiedzi
- [ ] `ArticleCrudTest`:
    - Test create (201)
    - Test create validation errors (422)
    - Test read (200)
    - Test update (200)
    - Test delete (204)
    - Test not found (404)
- [ ] `ArticleIdempotencyTest`:
    - Test pierwszego requestu (201)
    - Test duplikatu z tym samym key (409)
    - Test różnych key (oba 201)
- [ ] `AuthorCrudTest`:
    - Pełne CRUD testy
- [ ] `CategoryCrudTest`:
    - Pełne CRUD testy
    - Test parent/children relacji
- [ ] `TagCrudTest`:
    - Pełne CRUD testy
- [ ] `MediaUploadTest`:
    - Test upload (201)
    - Test invalid file type (422)
    - Test file too large (422)
    - Test delete (204)
- [ ] `RateLimitTest`:
    - Test przekroczenia limitu (429)
    - Test nagłówków rate limit

### [TASK-04.7] Unit Tests
Utworzyć w `tests/Unit/`:

- [ ] `ArticleDataTest`:
    - Test tworzenia DTO
    - Test `fromRequest()`
- [ ] `CreateArticleActionTest`:
    - Test tworzenia artykułu
    - Test generowania sluga
    - Test przypisywania tagów
- [ ] `UpdateArticleActionTest`:
    - Test aktualizacji pól
    - Test regeneracji sluga
- [ ] `IdempotencyServiceTest`:
    - Test `exists()`, `get()`, `store()`, `forget()`
- [ ] `MediaServiceTest`:
    - Test uploadu
    - Test usuwania

### [TASK-04.8] Documentation
Utworzyć/Zaktualizować:

- [ ] `README.md`:
    - Installation
    - Quick Start
    - Configuration
    - Using Custom Models
    - Events
    - API Reference
    - Testing
- [ ] `CHANGELOG.md`:
    - Format: Keep a Changelog
    - Sekcje: Added, Changed, Deprecated, Removed, Fixed, Security
- [ ] `LICENSE`:
    - MIT License

### [TASK-04.9] CI/CD
Utworzyć `.github/workflows/`:

- [ ] `tests.yml`:
    - Matrix: PHP 8.5, Laravel 11/12
    - Steps: checkout, setup PHP, composer install, run tests
    - Coverage report (opcjonalnie)
- [ ] `static-analysis.yml` (opcjonalnie):
    - PHPStan level 8

### [TASK-04.10] Facade (opcjonalnie)
Utworzyć w `src/Facades/`:

- [ ] `ArticleReceiver`:
    - Dostęp do głównych serwisów
    - Metody: `createArticle()`, `updateArticle()`, `uploadMedia()`

**Uwagi:**
- Testy powinny używać RefreshDatabase
- Events pozwalają użytkownikom na rozszerzenie funkcjonalności bez modyfikacji kodu paczki
- CI powinno działać na GitHub Actions
