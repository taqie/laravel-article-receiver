<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Article;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Actions\Tag\SyncArticleTagsAction;
use Taqie\LaravelArticleReceiver\Data\ArticleData;
use Taqie\LaravelArticleReceiver\Events\ArticleCreated;
use Taqie\LaravelArticleReceiver\Events\ArticleCreating;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class CreateArticleAction
{
    public function __construct(
        private readonly SyncArticleTagsAction $syncTags,
        private readonly HookService $hooks,
    ) {}

    public function execute(ArticleData $data): Article
    {
        $articleClass = config('article-receiver.models.article', Article::class);
        $article = new $articleClass;

        $data = $this->hooks->executeHook('before_create', $data) ?? $data;

        $creating = new ArticleCreating($data);
        event($creating);
        $data = $creating->data;

        $attributes = $this->buildAttributes($data);
        $article->fill($attributes);

        if (empty($article->slug) && ! empty($article->title)) {
            $article->slug = Str::slug($article->title);
        }

        $article->save();

        if (! empty($data->tags)) {
            $this->syncTags->execute($article, $data->tags);
        }

        event(new ArticleCreated($article, $data));
        $this->hooks->executeHook('after_create', $article, $data);

        return $article;
    }

    private function buildAttributes(ArticleData $data): array
    {
        $attributes = [];

        $attributes = [
            'title' => $data->title,
            'lead' => $data->lead,
            'meta_description' => $data->metaDescription,
            'body' => $data->body,
            'status' => $this->resolveStatus($data->status),
            'author_id' => $data->authorId ?? config('article-receiver.defaults.author_id'),
            'category_id' => $data->categoryId,
            'featured_image_url' => $data->featuredImageUrl,
            'published_at' => $data->publishedAt,
            'metadata' => $data->metadata,
            'idempotency_key' => $data->idempotencyKey,
        ];

        $attributes = $this->applyFieldMapping($attributes);

        return array_filter(
            $attributes,
            static fn ($value) => $value !== null
        );
    }

    private function applyFieldMapping(array $attributes): array
    {
        $mapping = config('article-receiver.field_mapping', []);
        $mapped = [];

        foreach ($attributes as $key => $value) {
            $target = $mapping[$key] ?? $key;
            $mapped[$target] = $value;
        }

        return $mapped;
    }

    private function resolveStatus(?string $status): string
    {
        $resolved = $status ?? (string) config('article-receiver.defaults.status', 'draft');
        $mapping = config('article-receiver.status_mapping', []);

        return $mapping[$resolved] ?? $resolved;
    }
}
