<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Actions\Tag\SyncArticleTagsAction;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Models\Tag;

it('syncs article tags with unique slugs', function (): void {
    $article = Article::query()->create([
        'title' => 'Test',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
    ]);

    $action = new SyncArticleTagsAction;

    $tags = $action->execute($article, ['AI', ' AI ', '', 'dev', 123, 'AI']);

    expect($tags)->toHaveCount(2)
        ->and(Tag::query()->count())->toBe(2)
        ->and($article->tags()->count())->toBe(2);
});
