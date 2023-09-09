<?php

namespace App\Tests\Entity;

use App\Entity\Card;
use App\Entity\CardInFold;
use App\Entity\Deck;
use App\Entity\GameRoomUser;
use App\Entity\Player;
use App\Entity\SkullKing;
use App\Entity\SkullKingPhase;
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

    /**
     * @throws Exception
     */
    public function test_initialize_players()
    {
        $firstGameRoomUser = $this->createAGameRoomUser();
        $secondGameRoomUser = $this->createAGameRoomUser();
        $game = new SkullKing(new ArrayCollection([$firstGameRoomUser, $secondGameRoomUser]));
        $deck = new Deck();
        $this->assertEquals(
            $game->getPlayers()[0],
            new Player($game,
                $firstGameRoomUser,
                [$deck->pop()]
            )
        );
        $this->assertEquals($game->getPlayers()[1],
            new Player($game,
                $secondGameRoomUser,
                [$deck->pop()]
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

        /** @var Player $playerOne */
        $playerOne = $game->getPlayers()[0];
        $playerOne->setId(1);
        /** @var Player $playerTwo */
        $playerTwo = $game->getPlayers()[1];
        $playerTwo->setId(2);

        $game->announce($playerOne->getUserId(), 1);
        $game->announce($playerTwo->getUserId(), 0);

        $this->assertEquals(1, $game->getPlayers()[0]->getAnnounce());
        $this->assertEquals(0, $game->getPlayers()[1]->getAnnounce());
    }

    /**
     * @throws Exception
     */
    public function test_play_card()
    {
        $firstGameRoomUser = $this->createAGameRoomUser();
        $secondGameRoomUser = $this->createAGameRoomUser();
        $game = new SkullKing(new ArrayCollection([$firstGameRoomUser, $secondGameRoomUser]));

        /** @var Player $playerOne */
        $playerOne = $game->getPlayers()[0];
        $playerOne->setId(1);
        /** @var Player $playerTwo */
        $playerTwo = $game->getPlayers()[1];
        $playerTwo->setId(2);

        $game->announce($firstGameRoomUser->getUserId(), 1);
        $game->announce($secondGameRoomUser->getUserId(), 0);

        $card = Card::create($playerOne->getCards()[0]);

        $game->playCard($firstGameRoomUser->getUserId(), $card->getId());
        $this->assertCount(1, $game->getFold()->getFold());
        $this->assertEquals(new ArrayCollection([new CardInFold($card->getId(), $playerOne->getId())]), $game->getFold()->getFold());

        $this->assertEquals($playerTwo->getId(), $game->getCurrentPlayerId());

        $this->assertEmpty($playerOne->getCards());

        $game->playCard($secondGameRoomUser->getUserId(), $playerTwo->getCards()[0]);
        $this->assertCount(0, $game->getFold()->getFold());
        $this->assertEquals(new ArrayCollection([]), $game->getFold()->getFold());
        $this->assertEquals(SkullKingPhase::ANNOUNCE->value, $game->getState());
        $this->assertEquals(2, $game->getNbRound());

    }

    public function createAGameRoomUser(): GameRoomUser
    {
        $gru = new GameRoomUser();
        $gru->setUserId(Uuid::v4());
        $gru->setUserName(Uuid::v4());

        return $gru;
    }


}