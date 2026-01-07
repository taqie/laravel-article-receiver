<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Author;

use Taqie\LaravelArticleReceiver\Models\Author;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class DeleteAuthorAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Author $author): bool
    {
        $this->hooks->executeHook('author.before_delete', $author);

        $deleted = (bool) $author->delete();

        if ($deleted) {
            $this->hooks->executeHook('author.after_delete', $author);
        }

        return $deleted;
    }
}
