<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'email',
        'bio',
        'avatar_url',
        'website',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
