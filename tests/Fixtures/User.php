<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
