<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Taqie\LaravelArticleReceiver\Data\MediaData;

it('builds media data from upload', function (): void {
    $file = UploadedFile::fake()->image('photo.jpg');

    $data = MediaData::fromUpload($file, 'Alt');

    expect($data->filename)->toBe('photo.jpg')
        ->and($data->mimeType)->toBe('image/jpeg')
        ->and($data->size)->toBeGreaterThan(0)
        ->and($data->altText)->toBe('Alt');
});
