<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Models\Author;

it('can create, update and delete an author', function (): void {
    $create = $this->postJson('/api/authors', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $create->assertCreated();

    $authorId = $create->json('id');

    $update = $this->putJson("/api/authors/{$authorId}", [
        'name' => 'Jane Updated',
    ]);

    $update->assertOk()->assertJsonFragment(['name' => 'Jane Updated']);

    $delete = $this->deleteJson("/api/authors/{$authorId}");
    $delete->assertNoContent();

    expect(Author::query()->find($authorId))->toBeNull();
});
