<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;

class CardInFold
{
    private Card $card;
    private Uuid $playerId;

    public function __construct(string $cardId, Uuid $playerId)
    {

        $this->card = Card::create($cardId);
        $this->playerId = $playerId;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function getPlayerId(): Uuid
    {
        return $this->playerId;
    }
}