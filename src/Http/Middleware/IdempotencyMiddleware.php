<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Taqie\LaravelArticleReceiver\Services\IdempotencyService;

class IdempotencyMiddleware
{
    public function __construct(private readonly IdempotencyService $service) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('article-receiver.idempotency.enabled', true)) {
            return $next($request);
        }

        $header = config('article-receiver.idempotency.header', 'X-Idempotency-Key');
        $rawKey = $request->header($header);

        if (! $rawKey) {
            return $next($request);
        }

        $key = $this->scopedKey($request, $rawKey);
        $ttl = (int) config('article-receiver.idempotency.ttl', 3600);
        $lock = Cache::lock("article-receiver:idempotency:lock:{$key}", $ttl);

        if (! $lock->get()) {
            return response()->json([
                'message' => 'Request is already being processed.',
            ], 409);
        }

        try {
            if ($this->service->exists($key)) {
                $cached = $this->service->get($key) ?? [];

                return response()->json([
                    'message' => 'Request already processed.',
                    ...$cached,
                ], 409);
            }

            $response = $next($request);

            if ($response->isSuccessful()) {
                $payload = method_exists($response, 'getData')
                    ? $response->getData(true)
                    : ['status' => $response->getStatusCode()];

                $this->service->store(
                    $key,
                    $payload,
                    $ttl
                );
            }

            return $response;
        } finally {
            $lock->release();
        }
    }

    private function scopedKey(Request $request, string $rawKey): string
    {
        $user = $request->user();
        $tokenId = optional($user?->currentAccessToken())->id;
        $userId = null;

        if ($user) {
            if (method_exists($user, 'getAuthIdentifier')) {
                $userId = $user->getAuthIdentifier();
            } elseif (method_exists($user, 'getKey')) {
                $userId = $user->getKey();
            }
        }

        $actor = $tokenId
            ? 'token:'.$tokenId
            : ($userId !== null ? 'user:'.$userId : 'ip:'.($request->ip() ?? 'unknown'));

        $scope = implode('|', [
            $actor,
            strtoupper($request->getMethod()),
            $request->path(),
            $rawKey,
        ]);

        return hash('sha256', $scope);
    }
}
