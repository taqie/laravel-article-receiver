<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => config('article-receiver.version', '0.1.0'),
        ]);
    }
}
