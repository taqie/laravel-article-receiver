<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Tag;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Data\TagData;
use Taqie\LaravelArticleReceiver\Models\Tag;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class UpdateTagAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Tag $tag, TagData $data): Tag
    {
        $data = $this->hooks->executeHook('tag.before_update', $tag, $data) ?? $data;

        $attributes = $this->buildAttributes($data);

        if (! isset($attributes['slug']) && isset($attributes['name'])) {
            $attributes['slug'] = Str::slug((string) $attributes['name']);
        }

        $tag->fill($attributes);
        $tag->save();

        $this->hooks->executeHook('tag.after_update', $tag, $data);

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
