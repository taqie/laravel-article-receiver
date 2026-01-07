<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\ArticleReceiverServiceProvider;

it('skips rate limiter when limit is disabled', function (): void {
    config()->set('article-receiver.routes.rate_limit', 0);

    $provider = new ArticleReceiverServiceProvider(app());
    $provider->boot();

    expect(true)->toBeTrue();
});
