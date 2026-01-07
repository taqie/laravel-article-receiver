<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Events;

use Taqie\LaravelArticleReceiver\Models\Media;

class MediaUploaded
{
    public function __construct(public Media $media) {}
}
