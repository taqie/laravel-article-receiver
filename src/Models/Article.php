<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Taqie\LaravelArticleReceiver\Traits\ReceivesArticles;

class Article extends Model
{
    use ReceivesArticles;

    public function getTable(): string
    {
        return (string) config('article-receiver.tables.article', parent::getTable());
    }

    protected $fillable = [
        'title',
        'slug',
        'lead',
        'meta_description',
        'body',
        'status',
        'author_id',
        'category_id',
        'featured_image_url',
        'published_at',
        'metadata',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            config('article-receiver.tables.article_tag', 'ar_article_tag')
        );
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }
}
