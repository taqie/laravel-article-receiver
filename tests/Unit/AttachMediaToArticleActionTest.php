<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Actions\Media\AttachMediaToArticleAction;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Models\Media;

it('attaches media to article', function (): void {
    $article = Article::query()->create([
        'title' => 'Test',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
    ]);

    $media = Media::query()->create([
        'filename' => 'file.jpg',
        'path' => 'articles/file.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 123,
    ]);

    $action = new AttachMediaToArticleAction;
    $action->execute($media, $article);

    $media->refresh();

    expect($media->article_id)->toBe($article->getKey());
});
