# EPIC 02: Domain Logic (Actions & Services)

Ten epic skupia się na implementacji logiki biznesowej w izolacji od warstwy HTTP. Wykorzystujemy wzorzec Actions oraz DTO.

## Założenia Architektoniczne
- **DTO (Data Transfer Objects):** Używamy klas `readonly` PHP 8.5+ do przenoszenia danych.
- **Actions:** Klasy realizujące konkretne przypadki użycia, przyjmujące DTO.
- **Services:** Logika infrastrukturalna (np. Media, Cache, Idempotency).

## Zadania

### [TASK-02.1] Data Transfer Objects (DTO)
Utworzyć w `src/Data/`:

- [ ] `ArticleData`:
    - Klasa `readonly`
    - Pola: `title`, `lead`, `metaDescription`, `body`, `tags` (array)
    - Pola: `authorId`, `categoryId`, `featuredImageUrl`, `publishedAt`, `metadata`
    - Pola: `status`, `idempotencyKey`
    - Metoda statyczna `fromRequest(array $data): self`
- [ ] `AuthorData`:
    - Pola: `name`, `email`, `bio`, `avatarUrl`, `website`
    - Metoda statyczna `fromRequest(array $data): self`
- [ ] `CategoryData`:
    - Pola: `name`, `slug`, `description`, `parentId`
    - Metoda statyczna `fromRequest(array $data): self`
- [ ] `TagData`:
    - Pola: `name`, `slug`
    - Metoda statyczna `fromRequest(array $data): self`
- [ ] `MediaData`:
    - Pola: `filename`, `path`, `disk`, `mimeType`, `size`, `altText`, `articleId`
    - Metoda statyczna `fromUpload(UploadedFile $file, ?string $altText): self`

### [TASK-02.2] Article Actions
Utworzyć w `src/Actions/Article/`:

- [ ] `CreateArticleAction`:
    - **Wejście:** `ArticleData $data`
    - **Logika:**
        - Utworzenie rekordu `Article` na podstawie DTO
        - Generowanie sluga (jeśli brak)
        - Obsługa relacji (przypisanie tagów, kategorii, autora)
        - Emitowanie eventu `ArticleCreating` przed zapisem
        - Emitowanie eventu `ArticleCreated` po zapisie
    - **Wyjście:** Instancja `Article`
- [ ] `UpdateArticleAction`:
    - **Wejście:** `Article $article`, `ArticleData $data`
    - **Logika:**
        - Aktualizacja pól modelu
        - Aktualizacja relacji (tagi)
        - Regeneracja sluga jeśli tytuł się zmienił
        - Emitowanie eventów `ArticleUpdating` / `ArticleUpdated`
    - **Wyjście:** Instancja `Article`
- [ ] `DeleteArticleAction`:
    - **Wejście:** `Article $article`
    - **Logika:**
        - Usunięcie powiązanych mediów (opcjonalnie)
        - Usunięcie artykułu
        - Emitowanie eventu `ArticleDeleted`
    - **Wyjście:** `bool`

### [TASK-02.3] Author Actions
Utworzyć w `src/Actions/Author/`:

- [ ] `CreateAuthorAction`:
    - **Wejście:** `AuthorData $data`
    - **Wyjście:** Instancja `Author`
- [ ] `UpdateAuthorAction`:
    - **Wejście:** `Author $author`, `AuthorData $data`
    - **Wyjście:** Instancja `Author`
- [ ] `DeleteAuthorAction`:
    - **Wejście:** `Author $author`
    - **Wyjście:** `bool`

### [TASK-02.4] Category Actions
Utworzyć w `src/Actions/Category/`:

- [ ] `CreateCategoryAction`:
    - **Wejście:** `CategoryData $data`
    - **Logika:** Auto-generowanie sluga
    - **Wyjście:** Instancja `Category`
- [ ] `UpdateCategoryAction`:
    - **Wejście:** `Category $category`, `CategoryData $data`
    - **Wyjście:** Instancja `Category`
- [ ] `DeleteCategoryAction`:
    - **Wejście:** `Category $category`
    - **Wyjście:** `bool`

### [TASK-02.5] Tag Actions
Utworzyć w `src/Actions/Tag/`:

- [ ] `CreateTagAction`:
    - **Wejście:** `TagData $data`
    - **Logika:** Auto-generowanie sluga
    - **Wyjście:** Instancja `Tag`
- [ ] `UpdateTagAction`:
    - **Wejście:** `Tag $tag`, `TagData $data`
    - **Wyjście:** Instancja `Tag`
- [ ] `DeleteTagAction`:
    - **Wejście:** `Tag $tag`
    - **Wyjście:** `bool`
- [ ] `SyncArticleTagsAction`:
    - **Wejście:** `Article $article`, `array $tagNames`
    - **Logika:** Znajdź lub utwórz tagi, zsynchronizuj z artykułem
    - **Wyjście:** `Collection` tagów

### [TASK-02.6] Media Service & Actions
Utworzyć w `src/Services/` oraz `src/Actions/Media/`:

- [ ] `MediaService`:
    - `upload(UploadedFile $file, ?string $folder): MediaData`
    - `delete(Media $media): bool`
    - Obsługa różnych disków (public, s3)
    - Walidacja typu i rozmiaru
- [ ] `UploadMediaAction`:
    - **Wejście:** `UploadedFile $file`, `?string $altText`, `?int $articleId`
    - **Wyjście:** Instancja `Media`
- [ ] `AttachMediaToArticleAction`:
    - **Wejście:** `Media $media`, `Article $article`
    - **Wyjście:** `Media`
- [ ] `DeleteMediaAction`:
    - **Wejście:** `Media $media`
    - **Logika:** Usunięcie pliku z dysku + rekordu z DB
    - **Wyjście:** `bool`

### [TASK-02.7] Idempotency Service
Utworzyć w `src/Services/`:

- [ ] `IdempotencyService`:
    - `exists(string $key): bool`
    - `get(string $key): ?array`
    - `store(string $key, array $response, int $ttl): void`
    - `forget(string $key): void`
    - Wykorzystanie Cache (Redis/Database)
    - Konfigurowalny TTL

### [TASK-02.8] Hook Service
Utworzyć w `src/Services/`:

- [ ] `HookService`:
    - `executeHook(string $hookName, mixed ...$params): void`
    - Obsługa callabli z configu
    - Obsługa klas z metodami (Class@method)

**Uwagi:**
- Actions powinny być niezależne od warstwy HTTP
- Każda Action powinna być testowalna jednostkowo
- DTO zapewniają type-safety i walidację na poziomie domeny
