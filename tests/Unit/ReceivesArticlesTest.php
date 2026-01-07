<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Taqie\LaravelArticleReceiver\Models\Article;

it('generates slug and detects remote articles', function (): void {
    $local = Article::query()->create([
        'title' => 'Local Title',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
    ]);

    $remote = Article::query()->create([
        'title' => 'Remote Title',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
        'idempotency_key' => 'remote-key',
    ]);

    expect($local->slug)->toBe('local-title')
        ->and($remote->isFromRemote())->toBeTrue()
        ->and(Article::query()->fromRemote()->count())->toBe(1);

    Route::get('/articles/{article}', fn () => 'ok')->name('articles.show');
    config()->set('article-receiver.url.route_name', 'articles.show');

    expect($local->article_url)->toContain('/articles/'.$local->getKey());
});
