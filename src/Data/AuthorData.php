<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Data;

final readonly class AuthorData
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $bio,
        public ?string $avatarUrl,
        public ?string $website,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            bio: $data['bio'] ?? null,
            avatarUrl: $data['avatar_url'] ?? ($data['avatarUrl'] ?? null),
            website: $data['website'] ?? null,
        );
    }
}
