<?php

namespace App\Entity;

class WinnerWithPotentialBonus
{
    private Player $player;

    private int $potentialBonus;

    public function __construct(Player $player, int $potentialBonus)
    {
        $this->player = $player;
        $this->potentialBonus = $potentialBonus;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getPotentialBonus(): int
    {
        return $this->potentialBonus;
    }


}