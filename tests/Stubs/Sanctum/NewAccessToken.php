<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum;

class NewAccessToken
{
    public function __construct(
        public PersonalAccessToken $accessToken,
        public string $plainTextToken,
    ) {}
}
