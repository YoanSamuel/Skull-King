<?php


namespace App\Service;


class CardPlayDecr implements CardPlay
{
    public function Play(array $cards, string $card) : array
    {

        if(count($cards) == 0) return [];
        foreach ($cards as $key => $cardToRemove) {
            if ($cardToRemove === $card) {
                unset($cards[$key]);
            }
        }
        return $cards;
    }
}