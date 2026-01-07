<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Taqie\LaravelArticleReceiver\Models\Media;
use Taqie\LaravelArticleReceiver\Services\MediaService;

it('uploads and deletes media', function (): void {
    Storage::fake('public');
    config()->set('article-receiver.media.disk', 'public');

    $service = new MediaService;
    $file = UploadedFile::fake()->image('photo.jpg');

    $data = $service->upload($file, null);

    expect($data->path)->not->toBe('');

    $media = Media::query()->create([
        'filename' => $data->filename,
        'path' => $data->path,
        'disk' => $data->disk,
        'mime_type' => $data->mimeType,
        'size' => $data->size,
    ]);

    expect($service->delete($media))->toBeTrue();
});

it('rejects unsupported media types', function (): void {
    Storage::fake('public');
    config()->set('article-receiver.media.disk', 'public');
    config()->set('article-receiver.media.allowed_types', ['image/png']);

    $service = new MediaService;
    $file = UploadedFile::fake()->image('photo.jpg');

    $service->upload($file, null);
})->throws(InvalidArgumentException::class);

it('rejects oversized media', function (): void {
    Storage::fake('public');
    config()->set('article-receiver.media.disk', 'public');
    config()->set('article-receiver.media.max_size', 1);

    $service = new MediaService;
    $file = UploadedFile::fake()->image('photo.jpg')->size(5 * 1024);

    $service->upload($file, null);
})->throws(InvalidArgumentException::class);

it('fails when media uploads are disabled', function (): void {
    config()->set('article-receiver.media.enabled', false);

    $service = new MediaService;
    $file = UploadedFile::fake()->image('photo.jpg');

    $service->upload($file, null);
})->throws(RuntimeException::class);
