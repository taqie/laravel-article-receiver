<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Tag;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Models\Tag;

final class SyncArticleTagsAction
{
    public function execute(Article $article, array $tagNames): Collection
    {
        $tagClass = config('article-receiver.models.tag', Tag::class);

        $tags = collect($tagNames)
            ->filter(fn ($name) => is_string($name) && $name !== '')
            ->map(fn (string $name) => trim($name))
            ->filter(fn (string $name) => $name !== '')
            ->unique()
            ->map(function (string $name) use ($tagClass) {
                $slug = Str::slug($name);

                return $tagClass::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name]
                );
            })
            ->values();

        $article->tags()->sync($tags->pluck('id')->all());

        return $tags;
    }
}
