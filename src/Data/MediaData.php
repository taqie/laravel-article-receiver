<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Data;

use Illuminate\Http\UploadedFile;

final readonly class MediaData
{
    public function __construct(
        public string $filename,
        public string $path,
        public string $disk,
        public string $mimeType,
        public int $size,
        public ?string $altText,
        public ?int $articleId,
    ) {}

    public static function fromUpload(UploadedFile $file, ?string $altText): self
    {
        return new self(
            filename: $file->getClientOriginalName(),
            path: '',
            disk: '',
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            size: $file->getSize() ?? 0,
            altText: $altText,
            articleId: null,
        );
    }
}
