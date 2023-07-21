<?php

namespace App\Controller\dto;

use App\Entity\Card;
use App\Entity\Player;
use App\Entity\SkullKing;

class CardDTO
{
    public string $cardType;
    public string $id;
    public ?Player $player;
    public ?SkullKing $skull;

    public function __construct(Card $card)
    {
        $this->cardType = $card->getCardType();
        $this->id = $card->getId();
        $this->player = $card->getPlayer();
        $this->skull = $card->getSkullKing();
    }
}