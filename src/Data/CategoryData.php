<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Data;

final readonly class CategoryData
{
    public function __construct(
        public ?string $name,
        public ?string $slug,
        public ?string $description,
        public ?int $parentId,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
        );
    }
}
