<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Actions\Author;

use Taqie\LaravelArticleReceiver\Data\AuthorData;
use Taqie\LaravelArticleReceiver\Models\Author;
use Taqie\LaravelArticleReceiver\Services\HookService;

final class UpdateAuthorAction
{
    public function __construct(private readonly HookService $hooks) {}

    public function execute(Author $author, AuthorData $data): Author
    {
        $data = $this->hooks->executeHook('author.before_update', $author, $data) ?? $data;

        $author->fill($this->buildAttributes($data));
        $author->save();

        $this->hooks->executeHook('author.after_update', $author, $data);

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
