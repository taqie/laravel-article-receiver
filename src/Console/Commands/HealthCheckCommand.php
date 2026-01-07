<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthCheckCommand extends Command
{
    protected $signature = 'article-receiver:health';

    protected $description = 'Run basic health checks for the article receiver package';

    public function handle(): int
    {
        $checks = [
            'Routes' => fn () => $this->checkRoutes(),
            'Database' => fn () => $this->checkDatabase(),
            'Models' => fn () => $this->checkModels(),
            'Media Disk' => fn () => $this->checkMediaDisk(),
        ];

        $ok = true;

        foreach ($checks as $label => $check) {
            try {
                $check();
                $this->info("[OK] {$label}");
            } catch (Throwable $exception) {
                $ok = false;
                $this->error("[FAIL] {$label}: {$exception->getMessage()}");
            }
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    private function checkRoutes(): void
    {
        $prefix = trim((string) config('article-receiver.routes.prefix', 'api'), '/');
        $needle = $prefix === '' ? '/health' : '/'.$prefix.'/health';

        $found = collect(Route::getRoutes())->contains(fn ($route) => $route->uri() === ltrim($needle, '/'));

        if (! $found) {
            throw new \RuntimeException('Health route not registered.');
        }
    }

    private function checkDatabase(): void
    {
        DB::connection()->getPdo();
    }

    private function checkModels(): void
    {
        $models = config('article-receiver.models', []);

        foreach ($models as $class) {
            if (! $class || ! class_exists($class)) {
                throw new \RuntimeException('Model class missing: '.(string) $class);
            }
        }
    }

    private function checkMediaDisk(): void
    {
        $disk = (string) config('article-receiver.media.disk', 'public');
        $directory = trim((string) config('article-receiver.media.directory', 'articles'), '/');
        $path = $directory.'/.health-check-'.uniqid('', true).'.txt';

        Storage::disk($disk)->put($path, 'ok');
        Storage::disk($disk)->delete($path);
    }
}
