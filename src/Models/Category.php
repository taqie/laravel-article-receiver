<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public function getTable(): string
    {
        return (string) config('article-receiver.tables.category', parent::getTable());
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
