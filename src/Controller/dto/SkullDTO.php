<?php

namespace App\Controller\dto;

use App\Entity\CardInFold;
use App\Entity\Fold;
use App\Entity\Player;
use App\Entity\SkullKing;

class SkullDTO
{
    public string $id;
    public array $fold;
    public array $players;
    public string $gameState;

    public function __construct(SkullKing $skullKing)
    {
        $this->id = $skullKing->getId();
        $this->fold = $this->convertFoldDto($skullKing->getFold());
        $this->players = $skullKing->getPlayers()->map(function (Player $player) {
            return new PlayerDTO($player);
        })->toArray();
        $this->gameState = $skullKing->getState();
    }

    private function convertFoldDto(Fold $fold)
    {
        return $fold->getFold()->map(function (CardInFold $cardInFold) {
            return new CardDTO($cardInFold->getCard(), $cardInFold->getPlayerId());
        })->toArray();
    }
}