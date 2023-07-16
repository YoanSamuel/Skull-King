<?php

namespace App\Controller\dto;

use App\Entity\Card;

class CardDTO
{
    public string $cardType;
    public string $id;

    public function __construct(Card $card)
    {
        $this->cardType = $card->getCardType();
        $this->id = $card->getId();
    }
}