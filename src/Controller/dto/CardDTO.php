<?php

namespace App\Controller\dto;

use App\Entity\Card;

class CardDTO
{
    public string $cardType;
    public int $id;
    public int $playerId;
    public ?string $color;
    public ?string $pirateName;
    public ?string $value;
    public ?int $skullKingId;

    public function __construct(Card $card)
    {
        $this->cardType = $card->getCardType();
        $this->id = $card->getId();
        $this->playerId = $card->getPlayer()->getId();
        $this->color = $card->getColor();
        $this->pirateName = $card->getPirateName();
        $this->value = $card->getValue();
        $this->skullKingId = $card->getSkullKing()?->getId();
    }


}