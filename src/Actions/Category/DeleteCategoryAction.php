<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Category;

use Taqie\LaravelArticleReceiver\Models\Category;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class DeleteCategoryAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Category $category): bool
    {
        $this->hooks->executeHook('category.before_delete', $category);

        $deleted = (bool) $category->delete();

        if ($deleted) {
            $this->hooks->executeHook('category.after_delete', $category);
        }

        return $deleted;
    }
}
