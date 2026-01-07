<?php

declare(strict_types=1);

return [
    'version' => '0.1.0',

    'routes' => [
        'enabled' => true,
        'prefix' => 'api',
        'middleware' => ['api', 'auth:sanctum'],
        'rate_limit' => 60,
    ],

    'models' => [
        'article' => \Taqie\LaravelArticleReceiver\Models\Article::class,
        'author' => \Taqie\LaravelArticleReceiver\Models\Author::class,
        'category' => \Taqie\LaravelArticleReceiver\Models\Category::class,
        'tag' => \Taqie\LaravelArticleReceiver\Models\Tag::class,
        'media' => \Taqie\LaravelArticleReceiver\Models\Media::class,
    ],

    'field_mapping' => [
        'title' => 'title',
        'lead' => 'lead',
        'meta_description' => 'meta_description',
        'body' => 'body',
        'status' => 'status',
        'author_id' => 'author_id',
        'category_id' => 'category_id',
        'featured_image_url' => 'featured_image_url',
        'published_at' => 'published_at',
        'metadata' => 'metadata',
    ],

    'status_mapping' => [
        'draft' => 'draft',
        'published' => 'published',
    ],

    'defaults' => [
        'status' => 'draft',
        'author_id' => null,
    ],

    'media' => [
        'enabled' => true,
        'disk' => 'public',
        'directory' => 'articles',
        'max_size' => 10240,
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
    ],

    'idempotency' => [
        'enabled' => true,
        'header' => 'X-Idempotency-Key',
        'ttl' => 3600,
    ],

    'url' => [
        'route_name' => null,
        'pattern' => '/articles/{slug}',
    ],

    'hooks' => [
        'before_create' => null,
        'after_create' => null,
        'before_update' => null,
        'after_update' => null,
        'before_delete' => null,
        'after_delete' => null,
        'author' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'category' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'tag' => [
            'before_create' => null,
            'after_create' => null,
            'before_update' => null,
            'after_update' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
        'media' => [
            'before_create' => null,
            'after_create' => null,
            'before_delete' => null,
            'after_delete' => null,
        ],
    ],

    'validation' => [
        'store_article' => [],
        'update_article' => [],
        'store_author' => [],
        'update_author' => [],
        'store_category' => [],
        'update_category' => [],
        'store_tag' => [],
        'update_tag' => [],
        'upload_media' => [],
    ],

    'response' => [
        'resource' => null,
        'resources' => [
            'article' => null,
            'author' => null,
            'category' => null,
            'tag' => null,
            'media' => null,
        ],
    ],
];
