<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Category;

use Illuminate\Support\Str;
use Taqie\LaravelArticleReceiver\Data\CategoryData;
use Taqie\LaravelArticleReceiver\Models\Category;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class CreateCategoryAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(CategoryData $data): Category
    {
        $categoryClass = config('article-receiver.models.category', Category::class);
        $category = new $categoryClass;

        $data = $this->hooks->executeHook('category.before_create', $data) ?? $data;

        $attributes = $this->buildAttributes($data);
        $category->fill($attributes);

        if (empty($category->slug) && ! empty($category->name)) {
            $category->slug = Str::slug($category->name);
        }

        $category->save();

        $this->hooks->executeHook('category.after_create', $category, $data);

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
