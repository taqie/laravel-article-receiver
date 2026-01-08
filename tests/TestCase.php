<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests;

use Illuminate\Support\Facades\Auth;
use Orchestra\Testbench\TestCase as Orchestra;
use Taqie\LaravelArticleReceiver\ArticleReceiverServiceProvider;
use Taqie\LaravelArticleReceiver\Tests\Fixtures\User;

if (! class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
    class_alias(
        \Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum\SanctumServiceProvider::class,
        \Laravel\Sanctum\SanctumServiceProvider::class
    );
}

if (! class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
    class_alias(
        \Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum\PersonalAccessToken::class,
        \Laravel\Sanctum\PersonalAccessToken::class
    );
}

if (! class_exists(\Laravel\Sanctum\NewAccessToken::class)) {
    class_alias(
        \Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum\NewAccessToken::class,
        \Laravel\Sanctum\NewAccessToken::class
    );
}

if (! trait_exists(\Laravel\Sanctum\HasApiTokens::class)) {
    class_alias(
        \Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum\HasApiTokens::class,
        \Laravel\Sanctum\HasApiTokens::class
    );
}

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ArticleReceiverServiceProvider::class,
            \Laravel\Sanctum\SanctumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('article-receiver.routes.middleware', ['api', 'auth:sanctum']);
        $app['config']->set('auth.guards.sanctum', [
            'driver' => 'sanctum',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Auth::viaRequest('sanctum', function () {
            return User::query()->first();
        });

        if (! User::query()->exists()) {
            User::query()->create([
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => 'secret',
            ]);
        }
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $sanctumMigrations = __DIR__.'/../vendor/laravel/sanctum/database/migrations';
        $stubTokensMigration = __DIR__.'/Database/migrations/2025_01_01_000001_create_personal_access_tokens_table.php';

        if (is_dir($sanctumMigrations) && ! file_exists($stubTokensMigration)) {
            $this->loadMigrationsFrom($sanctumMigrations);
        }
    }
}
