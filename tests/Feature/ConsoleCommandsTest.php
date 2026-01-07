<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\User;

it('runs install command', function (): void {
    $exit = Artisan::call('article-receiver:install', ['--no-interaction' => true]);

    expect($exit)->toBe(0);
});

it('generates sanctum token', function (): void {
    $user = User::query()->create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'secret',
    ]);

    $exit = Artisan::call('article-receiver:token', [
        '--user' => $user->getKey(),
        '--name' => 'test-token',
        '--abilities' => ['articles:create'],
    ]);

    expect($exit)->toBe(0)
        ->and(Artisan::output())->toContain('Token generated');
});

it('lists and revokes tokens', function (): void {
    $user = User::query()->create([
        'name' => 'Test2',
        'email' => 'test2@example.com',
        'password' => 'secret',
    ]);

    $token = $user->createToken('list-token')->accessToken;

    $exit = Artisan::call('article-receiver:tokens');
    expect($exit)->toBe(0);

    $exit = Artisan::call('article-receiver:tokens', ['--revoke' => $token->getKey()]);
    expect($exit)->toBe(0);
});

it('runs health check command', function (): void {
    Storage::fake('public');
    config()->set('article-receiver.media.disk', 'public');

    $exit = Artisan::call('article-receiver:health');

    expect($exit)->toBe(0);
});

it('fails when user model is missing', function (): void {
    config()->set('auth.providers.users.model', null);

    $exit = Artisan::call('article-receiver:token', ['--user' => 1]);

    expect($exit)->toBe(1);
});

it('fails when user is not found', function (): void {
    $exit = Artisan::call('article-receiver:token', ['--user' => 9999]);

    expect($exit)->toBe(1);
});

it('fails when user id is not provided', function (): void {
    $exit = Artisan::call('article-receiver:token', ['--no-interaction' => true]);

    expect($exit)->toBe(1);
});

it('fails when token to revoke is missing', function (): void {
    $exit = Artisan::call('article-receiver:tokens', ['--revoke' => 9999]);

    expect($exit)->toBe(1);
});
