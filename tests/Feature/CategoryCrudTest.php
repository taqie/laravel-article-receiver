<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Models\Category;

it('can create, update and delete categories', function (): void {
    $parent = $this->postJson('/api/categories', [
        'name' => 'Tech',
        'slug' => 'tech',
    ]);

    $parent->assertCreated();
    $parentId = $parent->json('id');

    $child = $this->postJson('/api/categories', [
        'name' => 'AI',
        'parent_id' => $parentId,
    ]);

    $child->assertCreated()->assertJsonFragment(['slug' => 'ai']);
    $childId = $child->json('id');

    $update = $this->putJson("/api/categories/{$childId}", [
        'name' => 'AI Updated',
    ]);

    $update->assertOk()->assertJsonFragment(['name' => 'AI Updated']);

    $this->deleteJson("/api/categories/{$childId}")->assertNoContent();
    $this->deleteJson("/api/categories/{$parentId}")->assertNoContent();

    expect(Category::query()->find($childId))->toBeNull();
});
