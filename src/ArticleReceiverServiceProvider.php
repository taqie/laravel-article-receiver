<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Taqie\LaravelArticleReceiver\Console\Commands\GenerateTokenCommand;
use Taqie\LaravelArticleReceiver\Console\Commands\HealthCheckCommand;
use Taqie\LaravelArticleReceiver\Console\Commands\InstallCommand;
use Taqie\LaravelArticleReceiver\Console\Commands\ListTokensCommand;
use Taqie\LaravelArticleReceiver\Http\Middleware\IdempotencyMiddleware;

class ArticleReceiverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/article-receiver.php', 'article-receiver');
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $this->registerMiddleware();
        $this->registerRateLimiter();

        $this->publishes([
            __DIR__.'/../config/article-receiver.php' => config_path('article-receiver.php'),
        ], 'article-receiver-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'article-receiver-migrations');

        $this->publishes([
            __DIR__.'/../stubs/tests' => base_path('tests'),
        ], 'article-receiver-tests');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                GenerateTokenCommand::class,
                HealthCheckCommand::class,
                ListTokensCommand::class,
            ]);
        }

        Route::bind('article', fn (string $value) => $this->resolveModel('article', $value));
        Route::bind('author', fn (string $value) => $this->resolveModel('author', $value));
        Route::bind('category', fn (string $value) => $this->resolveModel('category', $value));
        Route::bind('tag', fn (string $value) => $this->resolveModel('tag', $value));
        Route::bind('media', fn (string $value) => $this->resolveModel('media', $value));

        if (config('article-receiver.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }
    }

    private function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('article-receiver.idempotency', IdempotencyMiddleware::class);
    }

    private function registerRateLimiter(): void
    {
        $limit = (int) config('article-receiver.routes.rate_limit', 60);

        if ($limit <= 0) {
            return;
        }

        RateLimiter::for('article-receiver', function ($request) use ($limit) {
            $tokenId = optional($request->user()?->currentAccessToken())->id;
            $key = $tokenId ? 'token:'.$tokenId : $request->ip();

            return Limit::perMinute($limit)->by($key);
        });
    }

    private function resolveModel(string $key, string $value): mixed
    {
        $modelClass = config("article-receiver.models.{$key}");

        return $modelClass::query()->findOrFail($value);
    }
}
