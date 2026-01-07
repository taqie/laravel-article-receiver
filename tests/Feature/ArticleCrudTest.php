<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Models\Article;

it('can create, update and delete an article', function (): void {
    config()->set('article-receiver.status_mapping', [
        'draft' => 'DRAFT',
        'published' => 'PUBLISHED',
    ]);

    $payload = [
        'title' => 'Test Article',
        'lead' => 'Lead text',
        'meta_description' => 'Meta description',
        'body' => '<p>Body</p>',
        'status' => 'draft',
        'tags' => ['News', 'Tech'],
    ];

    $create = $this->postJson('/api/articles', $payload);
    $create->assertCreated();

    $articleId = $create->json('id');

    $update = $this->putJson("/api/articles/{$articleId}", [
        'title' => 'Updated Title',
        'status' => 'published',
        'tags' => ['Update'],
    ]);
    $update->assertOk()->assertJsonFragment(['title' => 'Updated Title']);

    $article = Article::query()->find($articleId);
    expect($article->status)->toBe('PUBLISHED')
        ->and($article->tags()->count())->toBe(1);

    $delete = $this->deleteJson("/api/articles/{$articleId}");
    $delete->assertNoContent();

    expect(Article::query()->find($articleId))->toBeNull();
});
