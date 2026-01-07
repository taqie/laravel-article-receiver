<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Data;

final readonly class TagData
{
    public function __construct(
        public ?string $name,
        public ?string $slug,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            slug: $data['slug'] ?? null,
        );
    }
}
