<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Taqie\LaravelArticleReceiver\Data\CategoryData;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $categoryId = $category?->getKey();
        $categoryTable = (string) config('article-receiver.tables.category', 'ar_categories');

        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($categoryTable, 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', "exists:{$categoryTable},id"],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.update_category', []));
    }

    public function toDto(): CategoryData
    {
        return CategoryData::fromRequest($this->validated());
    }
}
