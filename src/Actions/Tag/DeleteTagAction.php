<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Tag;

use Taqie\LaravelArticleReceiver\Models\Tag;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class DeleteTagAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Tag $tag): bool
    {
        $this->hooks->executeHook('tag.before_delete', $tag);

        $deleted = (bool) $tag->delete();

        if ($deleted) {
            $this->hooks->executeHook('tag.after_delete', $tag);
        }

        return $deleted;
    }
}
