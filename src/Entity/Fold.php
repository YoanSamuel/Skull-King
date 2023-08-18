<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

class Fold
{
    private Collection $fold;

    public function __construct(array $sortedPlayersId, array $fold)
    {
        $this->fold = new ArrayCollection();
        foreach($sortedPlayersId as $playerId){
            foreach($fold as $cardInfo) {

                if($cardInfo["player_id"] == $playerId) {

                    $this->fold->add(new CardInFold($cardInfo["card_id"], new Uuid($cardInfo["player_id"])));
                }
            }
        }
    }


    public function resolve() : ?CardInFold
    {

        $skullKing = $this->findCardByType( CardType::SKULLKING);
        $mermaid = $this->findCardByType(CardType::MERMAID);
        if(!is_null($skullKing)) {
            if(!is_null($mermaid)) {
               return $mermaid;
            }
            return $skullKing;
        }

        $pirate = $this->findCardByType(CardType::PIRATE);
        if(!is_null($pirate)) {
            return $pirate;
        }

        if(!is_null($mermaid)) {
            return $mermaid;
        }

        $blackCard = $this->findHighestColorValue(CardColor::BLACK);
        if(!is_null($blackCard)) {
            return $blackCard;
        }

        $firstcolorAsked = $this->findCardByType(CardType::COLORED);
        $colorAsked = is_null($firstcolorAsked)? null : CardColor::from($firstcolorAsked->getCard()->getColor());

        return $this->findHighestColorValue($colorAsked);

    }


    private function findCardByType(CardType $cardType) : ?CardInFold
    {
        return $this->fold->findFirst(function(int $key, CardInFold $cardInFold) use($cardType){
            return $cardInFold->getCard()->getCardType() == $cardType->value;
        });

    }

    private function findHighestColorValue(?CardColor $color) : ?CardInFold
    {
        return $this->fold->reduce(function(?CardInFold $acc, CardInFold $cardInFold) use($color){
            if($cardInFold->getCard()->getCardType() != CardType::COLORED->value) {
                return $acc;
            }

            if ($cardInFold->getCard()->getColor() != $color?->value) {
                return $acc;
            }

            if(is_null($acc)) {
                return $cardInFold;
            }

            if($acc->getCard()->getValue() < $cardInFold->getCard()->getValue()) {
                return $cardInFold;
            } else {
                return $acc;
            }


        });
    }
}

// objectif demain matin ordonner la fold, gagnant de la pli doit devenir nouveau premier joeuur
// a chaque pli gagné mettre a jour les scores.
//rajouter temps ou a ete pose la carte a ete jouée