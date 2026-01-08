<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Taqie\LaravelArticleReceiver\Data\ArticleData;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $authorTable = (string) config('article-receiver.tables.author', 'ar_authors');
        $categoryTable = (string) config('article-receiver.tables.category', 'ar_categories');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'lead' => ['required', 'string', 'max:500'],
            'meta_description' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'status' => ['nullable', 'string'],
            'author_id' => ['nullable', 'integer', "exists:{$authorTable},id"],
            'category_id' => ['nullable', 'integer', "exists:{$categoryTable},id"],
            'featured_image_url' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.store_article', []));
    }

    public function toDto(): ArticleData
    {
        $payload = $this->validated();
        $payload['idempotency_key'] = $this->header(config('article-receiver.idempotency.header', 'X-Idempotency-Key'));

        return ArticleData::fromRequest($payload);
    }
}
