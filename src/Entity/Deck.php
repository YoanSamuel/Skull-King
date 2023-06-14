<?php

namespace App\Entity;

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
            $this->cards[] = Card::mermaidCard();
            $this->cards[] = Card::mermaidCard();
            $this->cards[] = Card::scaryMaryCard();
        } else {

            $this->cards = $cards;
        }
    }


    public function pop()
    {
        return array_pop($this->cards);
    }


}