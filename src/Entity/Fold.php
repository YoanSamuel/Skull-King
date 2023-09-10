<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Fold
{
    private Collection $fold;

    public function __construct(array $sortedPlayersId, array $fold)
    {
        $this->fold = new ArrayCollection();
        foreach ($sortedPlayersId as $playerId) {
            foreach ($fold as $cardInfo) {

                if ($cardInfo["player_id"] == $playerId) {

                    $this->fold->add(new CardInFold($cardInfo["card_id"], $cardInfo["player_id"]));
                }
            }
        }
    }

    public function getFold(): Collection
    {
        return $this->fold;
    }


    public function resolve(): FoldResolved
    {

        $skullKing = $this->findCardByType(CardType::SKULLKING);
        $mermaid = $this->findCardByType(CardType::MERMAID);
        if (!is_null($skullKing)) {
            if (!is_null($mermaid)) {
                return new FoldResolved($mermaid, 50);
            }
            $countPirates = $this->fold->filter(fn(CardInFold $cardInFold) => $cardInFold->getCard()->getCardType() == CardType::PIRATE->value
            )->count();

            return new FoldResolved($skullKing, $countPirates * 30);
        }

        $pirate = $this->findCardByType(CardType::PIRATE);
        if (!is_null($pirate)) {
            return new FoldResolved($pirate);
        }

        if (!is_null($mermaid)) {
            return new FoldResolved($mermaid);
        }

        $blackCard = $this->findHighestColorValue(CardColor::BLACK);
        if (!is_null($blackCard)) {
            return new FoldResolved($blackCard);
        }

        $firstcolorAsked = $this->findCardByType(CardType::COLORED);
        $colorAsked = is_null($firstcolorAsked) ? null : CardColor::from($firstcolorAsked->getCard()->getColor());

        return new FoldResolved($this->findHighestColorValue($colorAsked));

    }


    private function findCardByType(CardType $cardType): ?CardInFold
    {
        return $this->fold->findFirst(function (int $key, CardInFold $cardInFold) use ($cardType) {
            return $cardInFold->getCard()->getCardType() == $cardType->value;
        });

    }

    private function findHighestColorValue(?CardColor $color): ?CardInFold
    {
        return $this->fold->reduce(function (?CardInFold $acc, CardInFold $cardInFold) use ($color) {
            if ($cardInFold->getCard()->getCardType() != CardType::COLORED->value) {
                return $acc;
            }

            if ($cardInFold->getCard()->getColor() != $color?->value) {
                return $acc;
            }

            if (is_null($acc)) {
                return $cardInFold;
            }

            if ($acc->getCard()->getValue() < $cardInFold->getCard()->getValue()) {
                return $cardInFold;
            } else {
                return $acc;
            }


        });
    }
}