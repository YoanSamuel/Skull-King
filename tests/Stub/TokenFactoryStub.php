<?php

namespace App\Tests\Stub;

use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;

class TokenFactoryStub implements TokenFactoryInterface
{

    public function create(?array $subscribe = [], ?array $publish = [], array $additionalClaims = []): string
    {
        return 'stubFactory';
    }
}