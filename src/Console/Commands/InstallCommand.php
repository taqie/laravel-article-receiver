<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'article-receiver:install';

    protected $description = 'Install the Laravel Article Receiver package';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'article-receiver-config']);
        $this->info('Config published.');

        if ($this->confirm('Publish migrations?', true)) {
            $this->call('vendor:publish', ['--tag' => 'article-receiver-migrations']);
            $this->info('Migrations published.');
        }

        if ($this->confirm('Run migrations now?', false)) {
            $this->call('migrate');
        }

        if ($this->confirm('Generate API token now?', false)) {
            $this->call('article-receiver:token');
        }

        return self::SUCCESS;
    }
}
