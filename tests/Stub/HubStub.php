<?php

namespace App\Tests\Stub;


use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\Update;

class HubStub implements HubInterface
{
    public function publish(Update $update): string
    {
        return 'id';
    }

    // implement rest of HubInterface methods here
    public function getUrl(): string
    {

        return 'url';
    }

    public function getPublicUrl(): string
    {
        return 'url';
    }

    public function getProvider(): TokenProviderInterface
    {
        return new TokenProviderStub();
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return new TokenFactoryStub();

    }
}