<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Events;

use Taqie\LaravelArticleReceiver\Data\ArticleData;

class ArticleCreating
{
    public function __construct(public ArticleData $data) {}
}
