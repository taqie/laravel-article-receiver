<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Taqie\LaravelArticleReceiver\Data\MediaData;
use Taqie\LaravelArticleReceiver\Models\Media;

final class MediaService
{
    public function upload(UploadedFile $file, ?string $folder): MediaData
    {
        if (! config('article-receiver.media.enabled', true)) {
            throw new RuntimeException('Media uploads are disabled.');
        }

        $disk = (string) config('article-receiver.media.disk', 'public');
        $maxSizeKb = (int) config('article-receiver.media.max_size', 10240);
        $allowedTypes = config('article-receiver.media.allowed_types', []);
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize() ?? 0;

        if (! empty($allowedTypes) && ! in_array($mimeType, $allowedTypes, true)) {
            throw new InvalidArgumentException('Unsupported media type.');
        }

        if ($maxSizeKb > 0 && $size > ($maxSizeKb * 1024)) {
            throw new InvalidArgumentException('File size exceeds the configured limit.');
        }

        $baseDirectory = trim((string) config('article-receiver.media.directory', 'articles'), '/');
        $folder = $this->sanitizeFolder($folder);
        $directory = $folder ?: $baseDirectory.'/'.now()->format('Y/m');
        $path = $file->store($directory, $disk);

        return new MediaData(
            filename: $file->getClientOriginalName(),
            path: $path,
            disk: $disk,
            mimeType: $mimeType,
            size: $size,
            altText: null,
            articleId: null,
        );
    }

    public function delete(Media $media): bool
    {
        Storage::disk($media->disk)->delete($media->path);

        return (bool) $media->delete();
    }

    private function sanitizeFolder(?string $folder): ?string
    {
        if ($folder === null) {
            return null;
        }

        $folder = trim($folder);

        if ($folder === '') {
            return null;
        }

        if (str_contains($folder, '..') || str_contains($folder, '\\') || str_starts_with($folder, '/')) {
            throw new InvalidArgumentException('Invalid media folder.');
        }

        if (! preg_match('/^[A-Za-z0-9_\/-]+$/', $folder)) {
            throw new InvalidArgumentException('Invalid media folder.');
        }

        return trim($folder, '/');
    }
}
