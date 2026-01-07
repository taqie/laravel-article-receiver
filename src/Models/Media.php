<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'article_id',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'alt_text',
    ];

    protected function url(): Attribute
    {
        return Attribute::get(function (): string {
            return Storage::disk($this->disk)->url($this->path);
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
