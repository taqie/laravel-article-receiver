<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Data;

final readonly class ArticleData
{
    public function __construct(
        public ?string $title,
        public ?string $lead,
        public ?string $metaDescription,
        public ?string $body,
        public ?array $tags,
        public ?int $authorId,
        public ?int $categoryId,
        public ?string $featuredImageUrl,
        public ?string $publishedAt,
        public ?array $metadata,
        public ?string $status,
        public ?string $idempotencyKey,
    ) {}

    public static function fromRequest(array $data): self
    {
        $tags = $data['tags'] ?? null;
        $metadata = $data['metadata'] ?? null;

        return new self(
            title: $data['title'] ?? null,
            lead: $data['lead'] ?? null,
            metaDescription: $data['meta_description'] ?? ($data['metaDescription'] ?? null),
            body: $data['body'] ?? null,
            tags: is_array($tags) ? $tags : null,
            authorId: isset($data['author_id']) ? (int) $data['author_id'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            featuredImageUrl: $data['featured_image_url'] ?? ($data['featuredImageUrl'] ?? null),
            publishedAt: $data['published_at'] ?? ($data['publishedAt'] ?? null),
            metadata: is_array($metadata) ? $metadata : null,
            status: $data['status'] ?? null,
            idempotencyKey: $data['idempotency_key'] ?? ($data['idempotencyKey'] ?? null),
        );
    }
}
