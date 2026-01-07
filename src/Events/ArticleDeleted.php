<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Events;

class ArticleDeleted
{
    public function __construct(
        public int $articleId,
        public array $articleData,
    ) {}
}
