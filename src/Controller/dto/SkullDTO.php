<?php

namespace App\Controller\dto;

use App\Entity\Card;
use App\Entity\Player;
use App\Entity\SkullKing;

class SkullDTO
{
    public string $id;
    public array $fold;
    public array $players;

    public function __construct(SkullKing $skullKing)
    {
        $this->id = $skullKing->getId();
        $this->fold = $skullKing->getFold()->map(function (Card $card) {
            return new CardDTO($card);
        })->toArray();
        $this->players = $skullKing->getPlayers()->map(function (Player $player) {
            return new PlayerDTO($player);
        })->toArray();
    }
}