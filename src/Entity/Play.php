<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;

class Play
{
    private string $card;

    public function __construct(Uuid $userId, string $card)
    {
        $this->card = $card;
    }

    /**
     * @return string
     */
    public function getCard(): string
    {
        return $this->card;
    }
}