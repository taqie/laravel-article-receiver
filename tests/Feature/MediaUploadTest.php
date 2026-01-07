<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Taqie\LaravelArticleReceiver\Models\Media;

it('can upload and delete media', function (): void {
    Storage::fake('public');

    config()->set('article-receiver.media.disk', 'public');
    config()->set('article-receiver.media.allowed_types', ['image/jpeg', 'image/png', 'image/webp']);

    $file = UploadedFile::fake()->image('photo.jpg');

    $upload = $this->post('/api/media', [
        'file' => $file,
        'alt_text' => 'Alt text',
    ]);

    $upload->assertCreated()->assertJsonStructure(['id', 'url', 'filename', 'mime_type', 'size', 'alt_text']);

    $mediaId = $upload->json('id');

    $delete = $this->deleteJson("/api/media/{$mediaId}");
    $delete->assertNoContent();

    expect(Media::query()->find($mediaId))->toBeNull();
});
