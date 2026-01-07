<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Author;

use Taqie\LaravelArticleReceiver\Data\AuthorData;
use Taqie\LaravelArticleReceiver\Models\Author;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class CreateAuthorAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(AuthorData $data): Author
    {
        $authorClass = config('article-receiver.models.author', Author::class);
        $author = new $authorClass;

        $data = $this->hooks->executeHook('author.before_create', $data) ?? $data;

        $author->fill($this->buildAttributes($data));
        $author->save();

        $this->hooks->executeHook('author.after_create', $author, $data);

        return $author;
    }

    private function buildAttributes(AuthorData $data): array
    {
        $attributes = [];

        foreach ([
            'name' => $data->name,
            'email' => $data->email,
            'bio' => $data->bio,
            'avatar_url' => $data->avatarUrl,
            'website' => $data->website,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
