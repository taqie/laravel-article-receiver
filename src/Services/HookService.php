<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Services;

use Illuminate\Support\Str;

final class HookService
{
    public function executeHook(string $hookName, mixed ...$params): mixed
    {
        $hook = config('article-receiver.hooks.'.$hookName);

        if (! $hook) {
            return null;
        }

        if (is_callable($hook)) {
            return $hook(...$params);
        }

        if (is_string($hook)) {
            if (Str::contains($hook, '@')) {
                [$class, $method] = explode('@', $hook, 2);
                $instance = app()->make($class);

                return $instance->{$method}(...$params);
            }

            if (class_exists($hook)) {
                $instance = app()->make($hook);

                return $instance(...$params);
            }

            return app()->call($hook, $params);
        }

        if (is_array($hook)) {
            if (isset($hook[0]) && is_string($hook[0]) && class_exists($hook[0])) {
                $hook[0] = app()->make($hook[0]);
            }

            return $hook(...$params);
        }

        return null;
    }
}
