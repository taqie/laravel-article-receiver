<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    public $collects = ArticleResource::class;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
