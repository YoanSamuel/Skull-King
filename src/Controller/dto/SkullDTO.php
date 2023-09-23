<?php

namespace App\Controller\dto;

use App\Entity\CardInFold;
use App\Entity\Fold;
use App\Entity\FoldResult;
use App\Entity\Player;
use App\Entity\PlayerAnnounce;
use App\Entity\SkullKing;
use Symfony\Component\Uid\Uuid;

class SkullDTO
{
    public string $id;
    public array $fold;
    public array $players;
    public ?int $currentPlayerId;
    public string $gameState;
    public array $scoreBoard = [];
    public int $roundNumber;


    public function __construct(SkullKing $skullKing, Uuid $currentUserId)
    {
        $this->id = $skullKing->getId();
        $this->currentPlayerId = $skullKing->getCurrentPlayerId();
        $this->fold = $this->convertFoldDto($skullKing->getFold());
        $this->players = $skullKing->getPlayers()->map(fn(Player $player) => new PlayerDTO($player, $currentUserId)
        )->toArray();

        $this->roundNumber = $skullKing->getNbRound();
        $this->gameState = $skullKing->getState();
        /** @var FoldResult $foldResult */
        foreach ($skullKing->getFoldResults() as $foldResult) {
            /** @var PlayerAnnounce $playerAnnounce */
            foreach ($foldResult->getPlayerAnnounces() as $playerAnnounce) {
                $playerAnnounceDTO = new PlayerAnnounceDTO($playerAnnounce, $foldResult->getNbRound());
                if (array_key_exists($playerAnnounce->getPlayerId(), $this->scoreBoard)) {

                    $this->scoreBoard[$playerAnnounce->getPlayerId()][] = $playerAnnounceDTO;

                } else {
                    $this->scoreBoard[$playerAnnounce->getPlayerId()] = [$playerAnnounceDTO];
                }
            }
        }

        if ($skullKing->getFoldResults()->isEmpty()) {
            foreach ($skullKing->getPlayers() as $player) {
                $this->scoreBoard[$player->getId()] = [];
            }
        }
    }

    private function convertFoldDto(Fold $fold)
    {
        return $fold->getFold()->map(function (CardInFold $cardInFold) {
            return new CardDTO($cardInFold->getCard(), $cardInFold->getPlayerId());
        })->toArray();
    }
}