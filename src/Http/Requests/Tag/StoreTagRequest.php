<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Taqie\LaravelArticleReceiver\Data\TagData;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tags', 'slug')],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.store_tag', []));
    }

    public function toDto(): TagData
    {
        return TagData::fromRequest($this->validated());
    }
}
