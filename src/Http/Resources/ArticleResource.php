<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'title' => $this->title,
            'lead' => $this->lead,
            'meta_description' => $this->meta_description,
            'body' => $this->body,
            'tags' => $this->whenLoaded(
                'tags',
                fn () => $this->tags->pluck('name')->all(),
                []
            ),
            'status' => $this->status,
            'url' => $this->article_url ?? null,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'featured_image_url' => $this->featured_image_url,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'metadata' => $this->metadata,
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
