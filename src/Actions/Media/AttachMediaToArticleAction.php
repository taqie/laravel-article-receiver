<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Media;

use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Models\Media;

final class AttachMediaToArticleAction
{
    public function execute(Media $media, Article $article): Media
    {
        $media->article()->associate($article);
        $media->save();

        return $media;
    }
}
