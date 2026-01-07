<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Console\Commands;

use Illuminate\Console\Command;

class GenerateTokenCommand extends Command
{
    protected $signature = 'article-receiver:token
        {--user= : User ID to generate the token for}
        {--name=article-receiver : Token name}
        {--abilities=* : Token abilities (repeatable)}';

    protected $description = 'Generate a Sanctum token for article receiver API';

    public function handle(): int
    {
        $userId = $this->option('user');
        $userModel = config('auth.providers.users.model');

        if (! $userModel || ! class_exists($userModel)) {
            $this->error('User model is not configured.');

            return self::FAILURE;
        }

        if (! $userId) {
            $userId = $this->ask('User ID');
        }

        $user = $userModel::query()->find($userId);

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $name = (string) $this->option('name');
        $abilities = $this->option('abilities');

        $token = $user->createToken($name, $abilities)->plainTextToken;

        $this->info('Token generated:');
        $this->line($token);

        return self::SUCCESS;
    }
}
