<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Taqie\LaravelArticleReceiver\Data\ArticleData;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:255'],
            'lead' => ['nullable', 'string', 'max:500'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'body' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'status' => ['nullable', 'string'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'featured_image_url' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];

        return array_replace_recursive($rules, config('article-receiver.validation.update_article', []));
    }

    public function toDto(): ArticleData
    {
        $payload = $this->validated();
        $payload['idempotency_key'] = $this->header(config('article-receiver.idempotency.header', 'X-Idempotency-Key'));

        return ArticleData::fromRequest($payload);
    }
}
