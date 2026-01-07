<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('fails when health route is missing', function (): void {
    config()->set('article-receiver.routes.prefix', 'missing');

    $exit = Artisan::call('article-receiver:health');

    expect($exit)->toBe(1);
});

it('fails when a model class is missing', function (): void {
    config()->set('article-receiver.routes.prefix', 'api');
    config()->set('article-receiver.models', [
        'article' => 'Missing\\Model',
    ]);

    $exit = Artisan::call('article-receiver:health');

    expect($exit)->toBe(1);
});
