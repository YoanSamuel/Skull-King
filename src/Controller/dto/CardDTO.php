<?php

namespace App\Controller\dto;

use App\Entity\Card;

class CardDTO
{
    public string $cardType;
    public string $id;
    public ?int $playerId;
    public int $proptime;
    public ?string $color;
    public ?string $pirateName;
    public ?string $value;


    public function __construct(Card $card, ?int $playerId = null)
    {
        $this->cardType = $card->getCardType();
        $this->id = $card->getId();
        $this->playerId = $playerId;
        $this->proptime = microtime(true);
        $this->color = $card->getColor();
        $this->pirateName = $card->getPirateName();
        $this->value = $card->getValue();
    }


}