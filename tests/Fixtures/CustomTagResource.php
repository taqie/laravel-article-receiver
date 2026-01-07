<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests\Fixtures;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'custom' => 'tag',
        ];
    }
}
