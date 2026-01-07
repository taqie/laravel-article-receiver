# EPIC 03: API Integration Layer

Ten epic łączy warstwę HTTP z logiką biznesową poprzez DTO i Actions.

## Zadania

### [TASK-03.1] Form Requests - Articles
Utworzyć w `src/Http/Requests/Article/`:

- [ ] `StoreArticleRequest`:
    - Zasady walidacji zgodne z docs (title, lead, meta_description, body - required)
    - Metoda `toDto(): ArticleData`
    - Obsługa `X-Idempotency-Key` header
- [ ] `UpdateArticleRequest`:
    - Walidacja opcjonalnych pól (wszystkie nullable)
    - Metoda `toDto(): ArticleData`

### [TASK-03.2] Form Requests - Authors
Utworzyć w `src/Http/Requests/Author/`:

- [ ] `StoreAuthorRequest`:
    - Walidacja: name (required), email (unique, nullable), bio, avatar_url, website
    - Metoda `toDto(): AuthorData`
- [ ] `UpdateAuthorRequest`:
    - Metoda `toDto(): AuthorData`

### [TASK-03.3] Form Requests - Categories
Utworzyć w `src/Http/Requests/Category/`:

- [ ] `StoreCategoryRequest`:
    - Walidacja: name (required), slug (unique), description, parent_id (exists)
    - Metoda `toDto(): CategoryData`
- [ ] `UpdateCategoryRequest`:
    - Metoda `toDto(): CategoryData`

### [TASK-03.4] Form Requests - Tags
Utworzyć w `src/Http/Requests/Tag/`:

- [ ] `StoreTagRequest`:
    - Walidacja: name (required), slug (unique)
    - Metoda `toDto(): TagData`
- [ ] `UpdateTagRequest`:
    - Metoda `toDto(): TagData`

### [TASK-03.5] Form Requests - Media
Utworzyć w `src/Http/Requests/Media/`:

- [ ] `UploadMediaRequest`:
    - Walidacja: file (required, image, max size), alt_text, folder
    - Obsługa konfigurowalnych limitów z configu

### [TASK-03.6] API Resources
Utworzyć w `src/Http/Resources/`:

- [ ] `ArticleResource`:
    - Transformacja modelu na JSON zgodnie ze specyfikacją API
    - Includowanie relacji (author, category, tags, media)
    - Generowanie URL artykułu
- [ ] `ArticleCollection`:
    - Paginacja
    - Meta informacje
- [ ] `AuthorResource`:
    - Pola: id, name, email, bio, avatar_url, website, articles_count
- [ ] `CategoryResource`:
    - Pola: id, name, slug, description, parent_id, articles_count
    - Opcjonalne: children (nested)
- [ ] `TagResource`:
    - Pola: id, name, slug, articles_count
- [ ] `MediaResource`:
    - Pola: id, url, filename, mime_type, size, alt_text, created_at

### [TASK-03.7] Article Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `ArticleController`:
    - `index()`: Lista artykułów z paginacją
    - `store(StoreArticleRequest $request, CreateArticleAction $action)`:
        - `$dto = $request->toDto();`
        - `$article = $action->execute($dto);`
        - Return `ArticleResource` (201)
    - `show(Article $article)`: Return `ArticleResource`
    - `update(UpdateArticleRequest $request, Article $article, UpdateArticleAction $action)`:
        - `$dto = $request->toDto();`
        - `$article = $action->execute($article, $dto);`
        - Return `ArticleResource`
    - `destroy(Article $article, DeleteArticleAction $action)`:
        - Return 204 No Content

### [TASK-03.8] Author Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `AuthorController`:
    - `index()`: Lista autorów
    - `store(StoreAuthorRequest $request, CreateAuthorAction $action)`
    - `show(Author $author)`
    - `update(UpdateAuthorRequest $request, Author $author, UpdateAuthorAction $action)`
    - `destroy(Author $author, DeleteAuthorAction $action)`

### [TASK-03.9] Category Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `CategoryController`:
    - `index()`: Lista kategorii (opcjonalnie: tree structure)
    - `store(StoreCategoryRequest $request, CreateCategoryAction $action)`
    - `show(Category $category)`
    - `update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action)`
    - `destroy(Category $category, DeleteCategoryAction $action)`

### [TASK-03.10] Tag Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `TagController`:
    - `index()`: Lista tagów
    - `store(StoreTagRequest $request, CreateTagAction $action)`
    - `show(Tag $tag)`
    - `update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action)`
    - `destroy(Tag $tag, DeleteTagAction $action)`

### [TASK-03.11] Media Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `MediaController`:
    - `store(UploadMediaRequest $request, UploadMediaAction $action)`:
        - Return `MediaResource` (201)
    - `destroy(Media $media, DeleteMediaAction $action)`:
        - Return 204 No Content

### [TASK-03.12] Health Controller
Utworzyć w `src/Http/Controllers/`:

- [ ] `HealthController`:
    - `__invoke()`: Return JSON z:
        - `status: "ok"`
        - `timestamp` (ISO 8601)
        - `version` (z configu lub composer.json)

### [TASK-03.13] Routes
Utworzyć w `routes/api.php`:

- [ ] Rejestracja endpointów:
    ```php
    Route::middleware(config('article-receiver.routes.middleware'))->group(function () {
        Route::get('/health', HealthController::class);

        Route::apiResource('articles', ArticleController::class);
        Route::apiResource('authors', AuthorController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('tags', TagController::class);

        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    });
    ```
- [ ] Warunkowe ładowanie routes w ServiceProviderze (jeśli `routes.enabled`)
- [ ] Konfigurowalny prefix z configu

### [TASK-03.14] Route Model Binding
Skonfigurować w ServiceProviderze:

- [ ] Binding modeli z configu (umożliwienie custom modeli):
    ```php
    Route::bind('article', fn ($value) => config('article-receiver.models.article')::findOrFail($value));
    ```

**Uwagi:**
- Controllery powinny być "cienkie" - logika w Actions
- Wszystkie odpowiedzi przez Resources dla spójności
- Obsługa błędów przez Laravel Exception Handler
