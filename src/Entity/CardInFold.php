<?php

namespace App\Entity;


class CardInFold
{
    private Card $card;
    private int $playerId;

    public function __construct(string $cardId, int $playerId)
    {

        $this->card = Card::create($cardId);
        $this->playerId = $playerId;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}