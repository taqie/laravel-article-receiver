<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Events;

use Taqie\LaravelArticleReceiver\Data\ArticleData;
use Taqie\LaravelArticleReceiver\Models\Article;

class ArticleCreated
{
    public function __construct(
        public Article $article,
        public ArticleData $data,
    ) {}
}
