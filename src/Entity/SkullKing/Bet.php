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
    public static function of(string $playerName, int $betValue): Bet
    {

        return new Bet($playerName, $betValue);
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