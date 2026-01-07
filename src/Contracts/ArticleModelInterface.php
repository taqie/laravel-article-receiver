<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Contracts;

interface ArticleModelInterface
{
    public function getArticleUrlAttribute(): string;

    public function getFillable(): array;
}
