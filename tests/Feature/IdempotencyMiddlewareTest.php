<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('skips idempotency when disabled', function (): void {
    config()->set('article-receiver.idempotency.enabled', false);

    Route::post('/idempotent-disabled', fn () => response()->noContent())
        ->middleware('article-receiver.idempotency');

    $response = $this->withHeader('X-Idempotency-Key', 'key-1')
        ->postJson('/idempotent-disabled');

    $response->assertNoContent();
});

it('stores response status when response has no data', function (): void {
    config()->set('article-receiver.idempotency.enabled', true);

    Route::post('/idempotent-status', fn () => response()->noContent())
        ->middleware('article-receiver.idempotency');

    $headers = ['X-Idempotency-Key' => 'key-2'];

    $first = $this->withHeaders($headers)->postJson('/idempotent-status');
    $first->assertNoContent();

    $second = $this->withHeaders($headers)->postJson('/idempotent-status');
    $second->assertStatus(409)->assertJsonFragment(['status' => 204]);
});
