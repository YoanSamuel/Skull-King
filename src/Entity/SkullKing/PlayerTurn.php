<?php

namespace App\Entity\SkullKing;

use App\Service\CardPlay;

class PlayerTurn
{
    private int $current;
    private array $activePlayers;
    private CardPlay $cardPlay;

    public static function init(array $playersNames, CardPlay $cardPlay): PlayerTurn
    {
        $players = array_map(fn(string $name) => Player::init($name, Player::$START_CARD_COUNT,  $cardPlay), $playersNames);
        return new PlayerTurn(0, $players, $cardPlay);
    }

    public function __construct(int $current, array $activePlayers, CardPlay $cardPlay)
    {
        $this->current = $current;
        $this->activePlayers = $activePlayers;
        $this->cardPlay = $cardPlay;
    }

    public function current(): Player
    {
        return $this->activePlayers[$this->current];
    }

    public function goNext(): void
    {
        $this->current = $this->nextIndex();
    }

    public function next(): Player
    {
        return $this->activePlayers[$this->nextIndex()];
    }

    public function goPrev(): void
    {
        $this->current = $this->prevIndex();
    }

    public function prev(): Player
    {
        return $this->activePlayers[$this->prevIndex()];
    }

    private function prevIndex(): int
    {
        return $this->current === 0 ? $this->count() - 1 : $this->current - 1;
    }


    public function activePlayers(): array
    {
        return $this->activePlayers;
    }

    public function winner()
    {
        if ($this->count() > 1) return null;
        return $this->current()->name();
    }

    public function nextIndex(): int
    {
        return $this->current === $this->count() - 1 ? 0 : $this->current + 1;
    }

    public function count(): int
    {
        return count($this->activePlayers);
    }
}