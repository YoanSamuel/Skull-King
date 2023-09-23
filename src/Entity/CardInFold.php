<?php

namespace App\Entity;


use DateTimeImmutable;

class CardInFold
{
    private Card $card;
    private int $playerId;
    private DateTimeImmutable $playedAt;


    public function __construct(string $cardId, int $playerId, string $proptime)
    {

        $this->card = Card::create($cardId);
        $this->playerId = $playerId;
        $this->playedAt = DateTimeImmutable::createFromFormat('U.u', $proptime);

    }

    public function getPlayedAt(): DateTimeImmutable
    {
        return $this->playedAt;
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