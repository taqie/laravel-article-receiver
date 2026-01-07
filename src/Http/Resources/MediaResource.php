<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'url' => $this->url,
            'filename' => $this->filename,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'alt_text' => $this->alt_text,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
