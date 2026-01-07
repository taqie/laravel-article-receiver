<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'articles_count' => $this->whenCounted('articles'),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
