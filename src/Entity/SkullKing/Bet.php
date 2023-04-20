<?php

namespace App\Entity\SkullKing;

class Bet
{
    private string $playerName;
    private int $betValue;



    public function __construct(string $playerName, int $betValue)
    {
        $this->playerName = $playerName;
        $this->betValue = $betValue;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getBetValue(): int
    {
        return $this->betValue;
    }




}