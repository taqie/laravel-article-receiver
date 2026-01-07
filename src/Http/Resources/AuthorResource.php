<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'website' => $this->website,
            'articles_count' => $this->whenCounted('articles'),
        ];
    }
}
