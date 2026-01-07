<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests\Fixtures;

use Taqie\LaravelArticleReceiver\Data\ArticleData;

class HookTarget
{
    public function mutate(ArticleData $data): ArticleData
    {
        return new ArticleData(
            title: $data->title ? $data->title.' (hooked)' : null,
            lead: $data->lead,
            metaDescription: $data->metaDescription,
            body: $data->body,
            tags: $data->tags,
            authorId: $data->authorId,
            categoryId: $data->categoryId,
            featuredImageUrl: $data->featuredImageUrl,
            publishedAt: $data->publishedAt,
            metadata: $data->metadata,
            status: $data->status,
            idempotencyKey: $data->idempotencyKey,
        );
    }
}
