<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait ReceivesArticles
{
    public static function bootReceivesArticles(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug) && ! empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function scopeFromRemote(Builder $query): Builder
    {
        return $query->whereNotNull('idempotency_key');
    }

    public function isFromRemote(): bool
    {
        return ! empty($this->idempotency_key);
    }

    public function getArticleUrlAttribute(): string
    {
        $routeName = config('article-receiver.url.route_name');

        if ($routeName) {
            return route($routeName, $this);
        }

        $pattern = (string) config('article-receiver.url.pattern', '/articles/{slug}');

        return url(str_replace(['{slug}', '{id}'], [$this->slug, (string) $this->getKey()], $pattern));
    }
}
