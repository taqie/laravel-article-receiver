<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Article;

use Taqie\LaravelArticleReceiver\Actions\Media\DeleteMediaAction;
use Taqie\LaravelArticleReceiver\Events\ArticleDeleted;
use Taqie\LaravelArticleReceiver\Models\Article;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class DeleteArticleAction
{
    public function __construct(
        private readonly HookService $hooks,
        private readonly DeleteMediaAction $deleteMedia,
    ) {}

    public function execute(Article $article): bool
    {
        $this->hooks->executeHook('before_delete', $article);

        $payload = $article->toArray();

        foreach ($article->media()->get() as $media) {
            $this->deleteMedia->execute($media);
        }

        $deleted = (bool) $article->delete();

        if ($deleted) {
            event(new ArticleDeleted((int) $article->getKey(), $payload));
            $this->hooks->executeHook('after_delete', $article->getKey(), $payload);
        }

        return $deleted;
    }
}
