<?php

namespace App\Tests\Entity;

use App\Entity\Deck;
use App\Entity\GameRoomUser;
use App\Entity\Player;
use App\Entity\SkullKing;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class SkullKingTest extends TestCase
{
    public function test_at_least_two_players()
    {
        $this->expectException(Exception::class);

        new SkullKing(new ArrayCollection([$this->createAGameRoomUser()]));

    }

    public function test_initialize_players()
    {
        $firstGameRoomUser = $this->createAGameRoomUser();
        $secondGameRoomUser = $this->createAGameRoomUser();
        $game = new SkullKing(new ArrayCollection([$firstGameRoomUser, $secondGameRoomUser]));
        $deck = new Deck();
        $this->assertEquals(
            $game->getPlayers()[0],
            new Player($game,
                $firstGameRoomUser->getUserId(),
                new ArrayCollection([$deck->pop()])
            )
        );
        $this->assertEquals($game->getPlayers()[1],
            new Player($game,
                $secondGameRoomUser->getUserId(),
                new ArrayCollection([$deck->pop()])
            )
        );

    }

    /**
     * @throws Exception
     */
    public function test_announce()
    {
        $firstGameRoomUser = $this->createAGameRoomUser();
        $secondGameRoomUser = $this->createAGameRoomUser();
        $game = new SkullKing(new ArrayCollection([$firstGameRoomUser, $secondGameRoomUser]));

        $game->announce($firstGameRoomUser->getUserId(), 1);
        $game->announce($secondGameRoomUser->getUserId(), 0);

        $this->assertEquals(1, $game->getPlayers()[0]->getAnnounce());
        $this->assertEquals(0, $game->getPlayers()[1]->getAnnounce());
    }


    public function createAGameRoomUser(): GameRoomUser
    {
        $gru = new GameRoomUser();
        $gru->setUserId(Uuid::v4());
        $gru->setUserName(Uuid::v4());

        return $gru;
    }


}