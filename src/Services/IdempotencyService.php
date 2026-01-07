<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Services;

use Illuminate\Support\Facades\Cache;

final class IdempotencyService
{
    public function exists(string $key): bool
    {
        return Cache::has($this->cacheKey($key));
    }

    public function get(string $key): ?array
    {
        return Cache::get($this->cacheKey($key));
    }

    public function store(string $key, array $response, int $ttl): void
    {
        Cache::put($this->cacheKey($key), $response, $ttl);
    }

    public function forget(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }

    private function cacheKey(string $key): string
    {
        return "article-receiver:idempotency:{$key}";
    }
}
