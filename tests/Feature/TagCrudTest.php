<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Models\Tag;

it('can create, update and delete a tag', function (): void {
    $create = $this->postJson('/api/tags', [
        'name' => 'Laravel',
    ]);

    $create->assertCreated()->assertJsonFragment(['slug' => 'laravel']);

    $tagId = $create->json('id');

    $update = $this->putJson("/api/tags/{$tagId}", [
        'name' => 'Laravel Updated',
    ]);

    $update->assertOk()->assertJsonFragment(['name' => 'Laravel Updated']);

    $delete = $this->deleteJson("/api/tags/{$tagId}");
    $delete->assertNoContent();

    expect(Tag::query()->find($tagId))->toBeNull();
});
