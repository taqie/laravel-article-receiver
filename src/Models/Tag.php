<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public function getTable(): string
    {
        return (string) config('article-receiver.tables.tag', parent::getTable());
    }

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            config('article-receiver.tables.article_tag', 'ar_article_tag')
        );
    }
}
