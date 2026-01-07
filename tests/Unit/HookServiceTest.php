<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Data\ArticleData;
use Taqie\LaravelArticleReceiver\Services\HookService;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\HookInvoker;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\HookTarget;

it('returns null when hook is not configured', function (): void {
    config()->set('article-receiver.hooks.missing', null);

    $service = new HookService;
    expect($service->executeHook('missing'))->toBeNull();
});

it('executes callable hooks', function (): void {
    config()->set('article-receiver.hooks.before_create', function (ArticleData $data): ArticleData {
        return new ArticleData(
            title: 'changed',
            lead: $data->lead,
            metaDescription: $data->metaDescription,
            body: $data->body,
            tags: $data->tags,
            authorId: $data->authorId,
            categoryId: $data->categoryId,
            featuredImageUrl: $data->featuredImageUrl,
            publishedAt: $data->publishedAt,
            metadata: $data->metadata,
            status: $data->status,
            idempotencyKey: $data->idempotencyKey,
        );
    });

    $service = new HookService;

    $data = new ArticleData(
        title: 'original',
        lead: null,
        metaDescription: null,
        body: null,
        tags: null,
        authorId: null,
        categoryId: null,
        featuredImageUrl: null,
        publishedAt: null,
        metadata: null,
        status: null,
        idempotencyKey: null,
    );

    $result = $service->executeHook('before_create', $data);

    expect($result->title)->toBe('changed');
});

it('executes class method hooks', function (): void {
    config()->set('article-receiver.hooks.before_update', HookTarget::class.'@mutate');

    $service = new HookService;

    $data = new ArticleData(
        title: 'original',
        lead: null,
        metaDescription: null,
        body: null,
        tags: null,
        authorId: null,
        categoryId: null,
        featuredImageUrl: null,
        publishedAt: null,
        metadata: null,
        status: null,
        idempotencyKey: null,
    );

    $result = $service->executeHook('before_update', $data);

    expect($result->title)->toBe('original (hooked)');
});

it('executes invokable class hooks', function (): void {
    config()->set('article-receiver.hooks.after_update', HookInvoker::class);

    $service = new HookService;

    $data = new ArticleData(
        title: 'original',
        lead: null,
        metaDescription: null,
        body: null,
        tags: null,
        authorId: null,
        categoryId: null,
        featuredImageUrl: null,
        publishedAt: null,
        metadata: null,
        status: null,
        idempotencyKey: null,
    );

    $result = $service->executeHook('after_update', $data);

    expect($result->title)->toBe('original (invoked)');
});

it('executes array callable hooks', function (): void {
    config()->set('article-receiver.hooks.after_create', [HookTarget::class, 'mutate']);

    $service = new HookService;

    $data = new ArticleData(
        title: 'original',
        lead: null,
        metaDescription: null,
        body: null,
        tags: null,
        authorId: null,
        categoryId: null,
        featuredImageUrl: null,
        publishedAt: null,
        metadata: null,
        status: null,
        idempotencyKey: null,
    );

    $result = $service->executeHook('after_create', $data);

    expect($result->title)->toBe('original (hooked)');
});

it('executes string callable hooks via app', function (): void {
    config()->set('article-receiver.hooks.after_delete', 'trim');

    $service = new HookService;
    $result = $service->executeHook('after_delete', ' value ');

    expect($result)->toBe('value');
});

it('returns null for unsupported hook definitions', function (): void {
    config()->set('article-receiver.hooks.before_delete', 123);

    $service = new HookService;

    expect($service->executeHook('before_delete'))->toBeNull();
});

it('attempts to call string hooks via the container', function (): void {
    config()->set('article-receiver.hooks.after_update', 'missing-hook');

    $service = new HookService;

    $service->executeHook('after_update', 'value');
})->throws(Error::class);
