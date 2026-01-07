<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class ListTokensCommand extends Command
{
    protected $signature = 'article-receiver:tokens {--revoke= : Revoke token by ID}';

    protected $description = 'List or revoke Sanctum tokens for API access';

    public function handle(): int
    {
        $revokeId = $this->option('revoke');

        if ($revokeId) {
            $token = PersonalAccessToken::query()->find($revokeId);

            if (! $token) {
                $this->error('Token not found.');

                return self::FAILURE;
            }

            $token->delete();
            $this->info('Token revoked.');

            return self::SUCCESS;
        }

        $tokens = PersonalAccessToken::query()
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'last_used_at', 'created_at']);

        $this->table(['ID', 'Name', 'Last Used', 'Created At'], $tokens->toArray());

        return self::SUCCESS;
    }
}
