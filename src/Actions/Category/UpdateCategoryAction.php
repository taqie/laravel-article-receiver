<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Category;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Data\CategoryData;
use Taqie\LaravelArticleReceiver\Models\Category;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class UpdateCategoryAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Category $category, CategoryData $data): Category
    {
        $data = $this->hooks->executeHook('category.before_update', $category, $data) ?? $data;

        $attributes = $this->buildAttributes($data);

        if (! isset($attributes['slug']) && isset($attributes['name'])) {
            $attributes['slug'] = Str::slug((string) $attributes['name']);
        }

        $category->fill($attributes);
        $category->save();

        $this->hooks->executeHook('category.after_update', $category, $data);

        return $category;
    }

    private function buildAttributes(CategoryData $data): array
    {
        $attributes = [];

        foreach ([
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'parent_id' => $data->parentId,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
