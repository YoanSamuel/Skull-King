<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Deck
{


    static function coloredCards()
    {
        $arrayColoredCards = [];
        $colors = [CardColor::BLACK, CardColor::BLUE, CardColor::YELLOW, CardColor::RED];
        foreach ($colors as $color) {
            for ($i = 1; $i <= 13; $i++) {
                $arrayColoredCards[] = Card::coloredCard($color, $i);
            }
        }


        return $arrayColoredCards;

    }


    public array $cards;

    public function __construct(array $cards = null)
    {
        if ($cards == null) {
            $this->cards = Deck::coloredCards();
            $this->cards[] = Card::pirateCard(PirateName::BADEYEJOE);
            $this->cards[] = Card::pirateCard(PirateName::BETTYBRAVE);
            $this->cards[] = Card::pirateCard(PirateName::HARRYTHEGIANT);
            $this->cards[] = Card::pirateCard(PirateName::TORTUGAJACK);
            $this->cards[] = Card::pirateCard(PirateName::EVILEMMY);
            $this->cards[] = Card::skullCard();
            $this->cards[] = Card::escapeCard();
            $this->cards[] = Card::escapeCard();
            $this->cards[] = Card::escapeCard();
            $this->cards[] = Card::escapeCard();
            $this->cards[] = Card::escapeCard();
            $this->cards[] = Card::mermaidCard(MermaidName::ELISABETH);
            $this->cards[] = Card::mermaidCard(MermaidName::MONIQUE);
            $this->cards[] = Card::scaryMaryCard();
            $this->shuffle();
        } else {
            $this->cards = $cards;
            $this->shuffle();
        }
    }

    public function shuffle(): bool
    {
        return shuffle($this->cards);
    }

    public function pop()
    {
        return array_pop($this->cards);
    }


    public function distribute(int $nbCards): array
    {
        $cards = [];
        for ($i = 0; $i < $nbCards; ++$i) {
            $cards[] = $this->pop();
        }
        return $cards;
    }

}