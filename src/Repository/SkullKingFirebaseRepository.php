<?php

namespace App\Repository;

use App\Entity\GameId;
use App\Entity\SkullKing\SkullKing;
use App\Entity\SkullKing\Player;
use App\Entity\User;
use Kreait\Firebase\Contract\Database;

class SkullKingFirebaseRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }


    /**
     * @throws \Kreait\Firebase\Exception\DatabaseException
     */
    public function project(GameId $gameId, SkullKing $skullking)
    {
        $this->database->getReference("games/" . $gameId->value())->set([
            'id' => $gameId->value(),
            'winner' => $skullking->winner(),
            'turn' => is_null($skullking->turn()) ? [] : [
                'current' => $skullking->turn()->current()->name(),
                'prev' => $skullking->turn()->prev()->name(),
                'next' => $skullking->turn()->next()->name(),
                'activePlayers' => array_map(function (Player $player) {
                    return [
                        'name' => $player->name(),
                        'dices' => $player->dices()
                    ];
                }, $skullking->turn()->activePlayers()),
            ],
            'playersNames' => $skullking->playersNames(),
            'lastBet' => is_null($skullking->lastBet()) ? [] : [
                'diceValue' => $skullking->lastBet()->diceValue()->value(),
                'diceNumber' => $skullking->lastBet()->diceNumber(),
                'playerName' => $skullking->lastBet()->playerName(),
            ]
        ]);
    }

    public function projectUser(User $user)
    {
        $userRefPath = "/users/" . $user->uuid();
        $this->database->getReference($userRefPath)->set([
            'uuid' => $user->uuid(),
            'name' => $user->name()
        ]);
    }
}