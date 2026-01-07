<?php

declare(strict_types=1);

namespace Taqie\LaravelArticleReceiver\Tests\Stubs\Sanctum;

use Illuminate\Support\Str;

trait HasApiTokens
{
    public function createToken(string $name, array $abilities = ['*']): NewAccessToken
    {
        $token = hash('sha256', Str::random(40));

        $accessToken = PersonalAccessToken::query()->create([
            'tokenable_type' => $this->getMorphClass(),
            'tokenable_id' => $this->getKey(),
            'name' => $name,
            'token' => $token,
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($accessToken, $token.'|'.$accessToken->getKey());
    }

    public function currentAccessToken(): ?object
    {
        return null;
    }
}
