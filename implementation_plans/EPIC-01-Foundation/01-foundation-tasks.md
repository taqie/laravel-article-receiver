# EPIC 01: Foundation & Database Layer

Celem tego epica jest przygotowanie fundamentów paczki, struktury plików, konfiguracji oraz warstwy danych (Migracje i Modele).

## Zadania

### [TASK-01.1] Inicjalizacja Struktury Paczki
- [ ] Zweryfikować `composer.json` (zależności, autoloading, namespace `Taqie\LaravelArticleReceiver\`).
- [ ] Utworzyć/Dopracować `ArticleReceiverServiceProvider`.
    - Bootowanie konfiguracji.
    - Bootowanie migracji.
    - Rejestracja routes.
- [ ] Skonfigurować środowisko testowe (`orchestra/testbench`) w `tests/TestCase.php`.

### [TASK-01.2] Konfiguracja
Utworzyć plik `config/article-receiver.php` z pełną konfiguracją:

- [ ] **routes** - konfiguracja routingu:
    - `enabled` (bool)
    - `prefix` (string)
    - `middleware` (array)
    - `rate_limit` (int)
- [ ] **models** - możliwość podmiany klas:
    - `article`
    - `author`
    - `category`
    - `tag`
    - `media`
- [ ] **field_mapping** - mapowanie pól API → DB
- [ ] **status_mapping** - mapowanie statusów (draft/published → wartości w DB)
- [ ] **defaults** - wartości domyślne:
    - `status`
    - `author_id`
- [ ] **media** - konfiguracja uploadów:
    - `enabled`
    - `disk`
    - `directory`
    - `max_size`
    - `allowed_types`
- [ ] **idempotency** - konfiguracja idempotentności:
    - `enabled`
    - `header`
    - `ttl`
- [ ] **url** - generowanie URL artykułów:
    - `route_name`
    - `pattern`
- [ ] **hooks** - callbacki:
    - `before_create`, `after_create`
    - `before_update`, `after_update`
    - `before_delete`, `after_delete`
- [ ] **validation** - override reguł walidacji
- [ ] **response** - konfiguracja odpowiedzi:
    - `resource` (klasa Resource)

### [TASK-01.3] Migracje Bazy Danych
Utworzyć pliki migracji w `database/migrations/`:
- [ ] `create_authors_table`:
    - id, name, email (unique, nullable), bio, avatar_url, website, timestamps
- [ ] `create_categories_table`:
    - id, name, slug (unique), description, parent_id (self-reference), timestamps
- [ ] `create_tags_table`:
    - id, name, slug (unique), timestamps
- [ ] `create_articles_table`:
    - id, title, slug (unique), lead, meta_description, body
    - status, author_id (FK), category_id (FK), featured_image_url
    - published_at, metadata (json), idempotency_key (unique)
    - timestamps, indeksy
- [ ] `create_article_tag_table` (pivot):
    - article_id (FK), tag_id (FK), primary key
- [ ] `create_media_table`:
    - id, article_id (FK, nullable), filename, path, disk
    - mime_type, size, alt_text, timestamps

### [TASK-01.4] Modele Eloquent
Utworzyć modele w `src/Models/` z odpowiednimi relacjami:

- [ ] `Author`:
    - hasMany Articles
    - fillable, casts
- [ ] `Category`:
    - hasMany Articles
    - belongsTo Category (parent)
    - hasMany Category (children)
    - fillable, casts
- [ ] `Tag`:
    - belongsToMany Articles
    - fillable, casts
- [ ] `Media`:
    - belongsTo Article
    - fillable, casts
    - accessor dla pełnego URL
- [ ] `Article`:
    - belongsTo Author
    - belongsTo Category
    - belongsToMany Tag
    - hasMany Media
    - casts (metadata jako array, dates)
    - accessor dla URL (`getArticleUrlAttribute`)
    - obsługa slug (auto-generowanie)

### [TASK-01.5] Contracts & Traits
Utworzyć w `src/Contracts/` oraz `src/Traits/`:

- [ ] `ArticleModelInterface`:
    - `getArticleUrlAttribute(): string`
    - `getFillable(): array`
- [ ] `ReceivesArticles` trait:
    - Auto-generowanie slug
    - Scope `fromRemote()`
    - Metoda `isFromRemote(): bool`
    - Accessor `article_url`

**Uwagi:**
Na tym etapie nie tworzymy jeszcze logiki biznesowej zapisu, jedynie struktury danych i konfiguracji.
