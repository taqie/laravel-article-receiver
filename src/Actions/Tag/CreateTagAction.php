<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Tag;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Data\TagData;
use Taqie\LaravelArticleReceiver\Models\Tag;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class CreateTagAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(TagData $data): Tag
    {
        $tagClass = config('article-receiver.models.tag', Tag::class);
        $tag = new $tagClass;

        $data = $this->hooks->executeHook('tag.before_create', $data) ?? $data;

        $attributes = $this->buildAttributes($data);
        $tag->fill($attributes);

        if (empty($tag->slug) && ! empty($tag->name)) {
            $tag->slug = Str::slug($tag->name);
        }

        $tag->save();

        $this->hooks->executeHook('tag.after_create', $tag, $data);

        return $tag;
    }

    private function buildAttributes(TagData $data): array
    {
        $attributes = [];

        foreach ([
            'name' => $data->name,
            'slug' => $data->slug,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
