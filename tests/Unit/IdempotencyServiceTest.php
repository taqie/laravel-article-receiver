<?php

declare(strict_types=1);

use Taqie\LaravelArticleReceiver\Services\IdempotencyService;

it('stores and retrieves idempotency responses', function (): void {
    $service = new IdempotencyService;

    $service->store('key-1', ['status' => 'ok'], 60);

    expect($service->exists('key-1'))->toBeTrue()
        ->and($service->get('key-1'))->toBe(['status' => 'ok']);

    $service->forget('key-1');

    expect($service->exists('key-1'))->toBeFalse();
});
