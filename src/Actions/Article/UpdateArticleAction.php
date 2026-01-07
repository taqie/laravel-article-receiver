<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Article;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Actions\Tag\SyncArticleTagsAction;
use Taqie\LaravelArticleReceiver\Data\ArticleData;
use Taqie\LaravelArticleReceiver\Events\ArticleUpdated;
use Taqie\LaravelArticleReceiver\Events\ArticleUpdating;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class UpdateArticleAction
{
    public function __construct(
        private readonly SyncArticleTagsAction $syncTags,
        private readonly HookService $hooks,
    ) {}

    public function execute(Article $article, ArticleData $data): Article
    {
        $data = $this->hooks->executeHook('before_update', $article, $data) ?? $data;

        $updating = new ArticleUpdating($article, $data);
        event($updating);
        $data = $updating->data;

        $attributes = $this->buildAttributes($data);

        if (array_key_exists('title', $attributes) && $attributes['title'] !== $article->title) {
            $article->slug = Str::slug((string) $attributes['title']);
        }

        $article->fill($attributes);
        $article->save();

        if ($data->tags !== null) {
            $this->syncTags->execute($article, $data->tags);
        }

        event(new ArticleUpdated($article, $data));
        $this->hooks->executeHook('after_update', $article, $data);

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
            'status' => $data->status ? $this->resolveStatus($data->status) : null,
            'author_id' => $data->authorId,
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
