<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Taqie\LaravelArticleReceiver\Data\AuthorData;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $authorTable = (string) config('article-receiver.tables.author', 'ar_authors');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique($authorTable, 'email')],
            'bio' => ['nullable', 'string'],
            'avatar_url' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'string', 'max:500'],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.store_author', []));
    }

    public function toDto(): AuthorData
    {
        return AuthorData::fromRequest($this->validated());
    }
}
