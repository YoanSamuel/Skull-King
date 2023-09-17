<?php

namespace App\Controller\dto;

use App\Entity\PlayerAnnounce;

class PlayerAnnounceDTO
{
    public int $announced;
    public int $done;
    public int $potentialBonus;
    public int $score;
    public int $roundNumber;


    public function __construct(PlayerAnnounce $playerAnnounce, int $roundNumber)
    {
        $this->announced = $playerAnnounce->getAnnounced();
        $this->done = $playerAnnounce->getDone();
        $this->potentialBonus = $playerAnnounce->getPotentialBonus();
        $this->score = $playerAnnounce->getScore($roundNumber);
        $this->roundNumber = $roundNumber;
    }


}