<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Media;

use Illuminate\Http\UploadedFile;
use Taqie\LaravelArticleReceiver\Events\MediaUploaded;
use Taqie\LaravelArticleReceiver\Models\Media;
use Taqie\LaravelArticleReceiver\Services\HookService;
use Taqie\LaravelArticleReceiver\Services\MediaService;

final class UploadMediaAction
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly HookService $hooks,
    ) {}

    public function execute(UploadedFile $file, ?string $altText = null, ?int $articleId = null, ?string $folder = null): Media
    {
        $this->hooks->executeHook('media.before_create', $file, $altText, $articleId, $folder);

        $data = $this->mediaService->upload($file, $folder);

        $mediaClass = config('article-receiver.models.media', Media::class);
        $media = new $mediaClass;

        $media->fill([
            'article_id' => $articleId,
            'filename' => $data->filename,
            'path' => $data->path,
            'disk' => $data->disk,
            'mime_type' => $data->mimeType,
            'size' => $data->size,
            'alt_text' => $altText,
        ]);

        $media->save();

        event(new MediaUploaded($media));
        $this->hooks->executeHook('media.after_create', $media);

        return $media;
    }
}
