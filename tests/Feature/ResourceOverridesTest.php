<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\CustomArticleResource;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\CustomAuthorResource;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\CustomCategoryResource;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\CustomMediaResource;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\CustomTagResource;

it('uses custom resources for all endpoints', function (): void {
    config()->set('article-receiver.response.resources.article', CustomArticleResource::class);
    config()->set('article-receiver.response.resources.author', CustomAuthorResource::class);
    config()->set('article-receiver.response.resources.category', CustomCategoryResource::class);
    config()->set('article-receiver.response.resources.tag', CustomTagResource::class);
    config()->set('article-receiver.response.resources.media', CustomMediaResource::class);

    $author = $this->postJson('/api/authors', ['name' => 'Author', 'email' => 'a@example.com']);
    $author->assertCreated()->assertJsonFragment(['custom' => 'author']);

    $category = $this->postJson('/api/categories', ['name' => 'Category', 'slug' => 'category']);
    $category->assertCreated()->assertJsonFragment(['custom' => 'category']);

    $tag = $this->postJson('/api/tags', ['name' => 'Tag', 'slug' => 'tag']);
    $tag->assertCreated()->assertJsonFragment(['custom' => 'tag']);

    $article = $this->postJson('/api/articles', [
        'title' => 'Custom',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
    ]);
    $article->assertCreated()->assertJsonFragment(['custom' => 'article']);

    $articleId = $article->json('id');
    $this->getJson('/api/articles')->assertOk()->assertJsonFragment(['custom' => 'article']);
    $this->getJson("/api/articles/{$articleId}")->assertOk()->assertJsonFragment(['custom' => 'article']);

    $authorId = $author->json('id');
    $this->getJson('/api/authors')->assertOk()->assertJsonFragment(['custom' => 'author']);
    $this->getJson("/api/authors/{$authorId}")->assertOk()->assertJsonFragment(['custom' => 'author']);

    $categoryId = $category->json('id');
    $this->getJson('/api/categories')->assertOk()->assertJsonFragment(['custom' => 'category']);
    $this->getJson("/api/categories/{$categoryId}")->assertOk()->assertJsonFragment(['custom' => 'category']);

    $tagId = $tag->json('id');
    $this->getJson('/api/tags')->assertOk()->assertJsonFragment(['custom' => 'tag']);
    $this->getJson("/api/tags/{$tagId}")->assertOk()->assertJsonFragment(['custom' => 'tag']);

    Storage::fake('public');
    config()->set('article-receiver.media.disk', 'public');

    $file = UploadedFile::fake()->image('photo.jpg');
    $media = $this->post('/api/media', ['file' => $file]);
    $media->assertCreated()->assertJsonFragment(['custom' => 'media']);
});
