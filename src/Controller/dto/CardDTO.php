<?php

namespace App\Controller\dto;

use App\Entity\Card;
use App\Entity\Player;

class CardDTO
{
    public string $cardType;
    public string $id;
    public ?int $playerId;
    public ?string $color;
    public ?string $pirateName;
    public ?string $value;


    public function __construct(Card $card, ?Player $player = null)
    {
        $this->cardType = $card->getCardType();
        $this->id = $card->getId();
        $this->playerId = $player?->getId();
        $this->color = $card->getColor();
        $this->pirateName = $card->getPirateName();
        $this->value = $card->getValue();
    }


}