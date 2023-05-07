<?php

namespace App\Entity\SkullKing;


use InvalidArgumentException;

class SkullKing
{
    private array $playersNames;
    private ?PlayerTurn $turn;
    private ?Bet $lastBet;


    public function __construct(array        $playersNames = [],
                                ?PlayerTurn  $turn = null,
                                ?Bet         $lastBet = null)
    {
        $this->playersNames = $playersNames;
        $this->lastBet = $lastBet;
        $this->turn = $turn;
    }

    public function join(string $name)
    {
        if ($this->hasAlreadyPlayerNamed($name)) {
            throw new InvalidArgumentException("Tu ne peux pas avoir le même prénom que ton copain!");
        } else if ($this->playersCount() == 6) {
            throw new InvalidArgumentException("Le nombre de joueurs maximum est atteint!");
        } else {
            $this->playersNames[] = $name;
        }

    }

    public function start(): void
    {
        if ($this->playersCount() < 2) {
            throw new InvalidArgumentException ("Tu ne peux pas jouer à moins de deux joueurs!");
        }

        shuffle($this->playersNames);
        $this->turn = PlayerTurn::init($this->playersNames, $this->diceLauncher);
    }

    public function bet(string $playerName, int $betValue)
    {
        if ($playerName != $this->currentPlayerName()) {
            throw new InvalidArgumentException("Tu essaies de tricher ce n'est pas à toi de jouer!");
        }

        $this->lastBet = Bet::of($playerName, $betValue);
        $this->turn->goNext();
    }

    public function lastBet(): ?Bet
    {
        return $this->lastBet;
    }

    public function hasAlreadyPlayerNamed(string $name): bool
    {
        return array_search($name, $this->playersNames) !== false;
    }

    public function currentPlayerName(): string
    {
        return $this->turn?->current()->name();
    }

    public function playersCount(): int
    {
        return count($this->playersNames);
    }

    public function turn(): ?PlayerTurn
    {
        return $this->turn;
    }

    public function playersNames(): array
    {
        return $this->playersNames;
    }

    public function winner(): ?string
    {
        return $this->turn?->winner();
    }
}