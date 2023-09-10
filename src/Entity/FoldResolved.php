<?php

namespace App\Entity;

class FoldResolved
{
    private ?CardInFold $cardInFold;

    private int $potentialBonus = 0;


    public function __construct(?CardInFold $cardInFold, int $potentialBonus = 0)
    {
        $this->cardInFold = $cardInFold;
        $this->potentialBonus = $potentialBonus;
    }

    public function getCardInFold(): ?CardInFold
    {
        return $this->cardInFold;
    }

    public function getPotentialBonus(): int
    {
        return $this->potentialBonus;
    }


}