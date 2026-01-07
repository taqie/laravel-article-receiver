<?php

declare(strict_types=1);

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

it('rate limits requests', function (): void {
    RateLimiter::for('article-receiver', fn ($request) => Limit::perMinute(1)->by($request->ip()));

    $this->getJson('/api/health')->assertOk();
    $this->getJson('/api/health')->assertStatus(429);
});
