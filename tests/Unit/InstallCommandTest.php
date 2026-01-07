<?php

declare(strict_types=1);

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Taqie\LaravelArticleReceiver\Console\Commands\InstallCommand;

it('runs optional install steps when confirmed', function (): void {
    $command = new class extends InstallCommand
    {
        public array $calls = [];

        public function confirm($question, $default = false): bool
        {
            return true;
        }

        public function call($command, array $arguments = [])
        {
            $this->calls[] = $command;

            return 0;
        }
    };

    $command->setLaravel(app());
    $command->setOutput(new OutputStyle(new ArrayInput([]), new BufferedOutput));

    $exit = $command->handle();

    expect($exit)->toBe(0)
        ->and($command->calls)->toContain('vendor:publish')
        ->and($command->calls)->toContain('migrate')
        ->and($command->calls)->toContain('article-receiver:token');
});
