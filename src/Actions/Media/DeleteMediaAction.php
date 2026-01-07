<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Media;

use Taqie\LaravelArticleReceiver\Models\Media;
use Taqie\LaravelArticleReceiver\Services\HookService;
use Taqie\LaravelArticleReceiver\Services\MediaService;

final class DeleteMediaAction
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly HookService $hooks,
    ) {}

    public function execute(Media $media): bool
    {
        $this->hooks->executeHook('media.before_delete', $media);

        $deleted = $this->mediaService->delete($media);

        if ($deleted) {
            $this->hooks->executeHook('media.after_delete', $media);
        }

        return $deleted;
    }
}
