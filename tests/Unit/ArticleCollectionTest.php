<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Taqie\LaravelArticleReceiver\Http\Resources\ArticleCollection;
use Taqie\LaravelArticleReceiver\Models\Article;

it('transforms article collection', function (): void {
    $articles = collect([
        Article::query()->create([
            'title' => 'A',
            'lead' => 'Lead',
            'meta_description' => 'Meta',
            'body' => 'Body',
        ]),
        Article::query()->create([
            'title' => 'B',
            'lead' => 'Lead',
            'meta_description' => 'Meta',
            'body' => 'Body',
        ]),
    ]);

    $collection = new ArticleCollection($articles);
    $payload = $collection->toArray(Request::create('/'));

    expect($payload)->toBeArray()
        ->and($payload[0]['title'])->toBe('A');
});
