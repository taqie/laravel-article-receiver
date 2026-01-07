<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Models\Author;
use Taqie\LaravelArticleReceiver\Models\Category;
use Taqie\LaravelArticleReceiver\Models\Media;
use Taqie\LaravelArticleReceiver\Models\Tag;

it('exposes model relations and accessors', function (): void {
    Storage::fake('public');

    $author = Author::query()->create(['name' => 'Author']);
    $parent = Category::query()->create(['name' => 'Parent', 'slug' => 'parent']);
    $child = Category::query()->create(['name' => 'Child', 'slug' => 'child', 'parent_id' => $parent->getKey()]);

    $article = Article::query()->create([
        'title' => 'Title',
        'lead' => 'Lead',
        'meta_description' => 'Meta',
        'body' => 'Body',
        'author_id' => $author->getKey(),
        'category_id' => $child->getKey(),
    ]);

    $tag = Tag::query()->create(['name' => 'Tag', 'slug' => 'tag']);
    $article->tags()->sync([$tag->getKey()]);

    $media = Media::query()->create([
        'article_id' => $article->getKey(),
        'filename' => 'file.jpg',
        'path' => 'articles/file.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 10,
    ]);

    expect($author->articles()->get())->toHaveCount(1)
        ->and($child->parent()->first()->getKey())->toBe($parent->getKey())
        ->and($parent->children()->get())->toHaveCount(1)
        ->and($article->author()->first()->getKey())->toBe($author->getKey())
        ->and($article->category()->first()->getKey())->toBe($child->getKey())
        ->and($article->tags()->get())->toHaveCount(1)
        ->and($article->media()->get())->toHaveCount(1)
        ->and($tag->articles()->get())->toHaveCount(1)
        ->and($media->url)->toContain('articles/file.jpg');
});
