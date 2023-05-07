<?php

namespace App\Entity\SkullKing;

use App\Service\CardPlay;


class Player
{
    public static int $START_CARD_COUNT = 1;
    private string $name;
    private array $cards;


    public static function init(string $name, int $cardCount, CardPlay $cardPlay)
    {
        return new Player(
            $name,
            $cardPlay->Play(Player::cards(), $card),
        );
    }

    public function __construct(string $name, array $cards)
    {
        $this->name = $name;
        $this->cards = $cards;
    }


    public function name(): string
    {
        return $this->name;
    }

    public function cards(): array
    {
        return $this->cards;
    }

}

