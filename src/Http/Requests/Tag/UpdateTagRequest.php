<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Taqie\LaravelArticleReceiver\Data\TagData;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');
        $tagId = $tag?->getKey();

        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($tagId)],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.update_tag', []));
    }

    public function toDto(): TagData
    {
        return TagData::fromRequest($this->validated());
    }
}
