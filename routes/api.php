<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Taqie\LaravelArticleReceiver\Http\Controllers\ArticleController;
use Taqie\LaravelArticleReceiver\Http\Controllers\AuthorController;
use Taqie\LaravelArticleReceiver\Http\Controllers\CategoryController;
use Taqie\LaravelArticleReceiver\Http\Controllers\HealthController;
use Taqie\LaravelArticleReceiver\Http\Controllers\MediaController;
use Taqie\LaravelArticleReceiver\Http\Controllers\TagController;

Route::prefix(config('article-receiver.routes.prefix', 'api'))
    ->middleware(array_filter([
        ...config('article-receiver.routes.middleware', ['api']),
        config('article-receiver.idempotency.enabled', true) ? 'article-receiver.idempotency' : null,
        config('article-receiver.routes.rate_limit', 60) > 0 ? 'throttle:article-receiver' : null,
    ]))
    ->group(function (): void {
        Route::get('/health', HealthController::class);

        Route::apiResource('articles', ArticleController::class)->except(['update']);
        Route::apiResource('authors', AuthorController::class)->except(['update']);
        Route::apiResource('categories', CategoryController::class)->except(['update']);
        Route::apiResource('tags', TagController::class)->except(['update']);

        Route::match(['PUT', 'PATCH'], '/articles/{article}', [ArticleController::class, 'update']);
        Route::match(['PUT', 'PATCH'], '/authors/{author}', [AuthorController::class, 'update']);
        Route::match(['PUT', 'PATCH'], '/categories/{category}', [CategoryController::class, 'update']);
        Route::match(['PUT', 'PATCH'], '/tags/{tag}', [TagController::class, 'update']);

        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    });
