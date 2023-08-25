<?php

namespace App\Tests\Stub;

use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

class TokenProviderStub implements TokenProviderInterface
{

    public function getJwt(): string
    {
        return 'stub_JWT';
    }
}