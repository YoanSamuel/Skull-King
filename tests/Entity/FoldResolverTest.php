<?php

namespace App\Tests\Entity;

use App\Entity\CardInFold;
use App\Entity\Fold;
use App\Entity\FoldResolved;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FoldResolverTest extends TestCase
{

    public function test_resolve_fold()
    {
        $expectedWinner1 = [
            "player_id" => 2,
            "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "player_name" => "Arthur",
            "card_type" => "COLORED",
            "card_value" => "11",
            "card_pirate" => null,
            "card_mermaid" => null,
            "card_color" => "BLACK",
            "card_id" => "BLACK_11",
            'prop_time' => (new DateTimeImmutable())->sub(new DateInterval('PT2S'))->format('U.u')

        ];
        $fold = new Fold(
            [
                [
                    "player_id" => 1,
                    "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "COLORED",
                    "card_value" => "2",
                    "card_pirate" => null,
                    "card_mermaid" => null,
                    "card_color" => "RED",
                    "card_id" => "RED_2",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')
                ],
                $expectedWinner1
            ]);


        $expectedWinner3 = [
            "player_id" => 2,
            "player_userid" => "f3798fc3-e4c3-4482-b21d-10ae533056d7",
            "player_name" => "Davy",
            "card_type" => "MERMAID",
            "card_value" => null,
            "card_pirate" => null,
            "card_mermaid" => "MONIQUE",
            "card_color" => null,
            "card_id" => "MONIQUE_MERMAID",
            'prop_time' => (new DateTimeImmutable())->format('U.u')

        ];
        $fold2 = new Fold(
            [
                $expectedWinner3,
                [
                    "player_id" => 3,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60a7e",
                    "player_name" => "Arthur",
                    "card_type" => "PIRATE",
                    "card_value" => null,
                    "card_pirate" => "HARRYTHEGIANT",
                    "card_mermaid" => null,
                    "card_color" => null,
                    "card_id" => "HARRYTHEGIANT_PIRATE",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],
                [
                    "player_id" => 1,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Davy",
                    "card_type" => "SKULLKING",
                    "card_value" => null,
                    "card_pirate" => null,
                    "card_mermaid" => null,
                    "card_color" => null,
                    "card_id" => "SKULLKING",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],

            ]);

        $expectedWinner4 = [
            "player_id" => 1,
            "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "player_name" => "Morgan",
            "card_type" => "PIRATE",
            "card_value" => null,
            "card_pirate" => "TORTUGAJACK",
            "card_color" => null,
            "card_id" => "TORTUGAJACK_PIRATE",
            'prop_time' => (new DateTimeImmutable())->format('U.u')

        ];
        $fold3 = new Fold([
            $expectedWinner4,
            [
                "player_id" => 2,
                "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "BETTYBRAVE",
                "card_color" => null,
                "card_id" => "BETTYBRAVE_PIRATE",
                'prop_time' => (new DateTimeImmutable())->format('U.u')

            ],
            [
                "player_id" => 3,
                "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60a7e",
                "player_name" => "Arthur",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "HARRYTHEGIANT",
                "card_color" => null,
                "card_id" => "HARRYTHEGIANT_PIRATE",
                'prop_time' => (new DateTimeImmutable())->format('U.u')

            ],

        ]);

        $expectedWinner5 = [
            "player_id" => 1,
            "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "player_name" => "Morgan",
            "card_type" => "MERMAID",
            "card_value" => null,
            "card_pirate" => null,
            "card_mermaid" => "ELISABETH",
            "card_color" => null,
            "card_id" => "ELISABETH_MERMAID",
            'prop_time' => (new DateTimeImmutable())->format('U.u')

        ];
        $fold4 = new Fold([
            $expectedWinner5,
            [
                "player_id" => 1,
                "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "MONIQUE",
                "card_color" => null,
                "card_id" => "MONIQUE_MERMAID",
                'prop_time' => (new DateTimeImmutable())->format('U.u')

            ],

        ]);


        $result = $fold->resolve();
        $this->assertEquals($result, new FoldResolved(new CardInFold(
            $expectedWinner1['card_id'],
            $expectedWinner1['player_id'],
            $expectedWinner1['prop_time'])));


        $result2 = $fold2->resolve();
        $this->assertEquals($result2, new FoldResolved(new CardInFold(
            $expectedWinner3['card_id'],
            $expectedWinner3['player_id'],
            $expectedWinner3['prop_time']), 50));

        $result3 = $fold3->resolve();
        $this->assertEquals($result3, new FoldResolved(new CardInFold(
            $expectedWinner4['card_id'],
            $expectedWinner4['player_id'],
            $expectedWinner4['prop_time'])));

        $result4 = $fold4->resolve();
        $this->assertEquals($result4, new FoldResolved(new CardInFold(
            $expectedWinner5['card_id'],
            $expectedWinner5['player_id'],
            $expectedWinner5['prop_time'])));


    }

    public function test_color_asked()
    {
        $expectedWinner2 = [
            "player_id" => 1,
            "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "player_name" => "Morgan",
            "card_type" => "COLORED",
            "card_value" => "12",
            "card_pirate" => null,
            "card_mermaid" => null,
            "card_color" => "RED",
            "card_id" => "RED_12",
            'prop_time' => (new DateTimeImmutable())->sub(new DateInterval('PT2S'))->format('U.u')

        ];
        $fold1 = new Fold(
            [
                $expectedWinner2,
                [
                    "player_id" => 2,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Arthur",
                    "card_type" => "COLORED",
                    "card_value" => "11",
                    "card_pirate" => null,
                    "card_mermaid" => null,
                    "card_color" => "BLUE",
                    "card_id" => "BLUE_11",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ]
            ]);

        $result1 = $fold1->resolve();
        $this->assertEquals($result1, new FoldResolved(new CardInFold(
            $expectedWinner2['card_id'],
            $expectedWinner2['player_id'],
            $expectedWinner2['prop_time'])));
    }

    public function test_all_players_played_escape_should_return_null()
    {

        $fold5 = new Fold(
            [
                [
                    "player_id" => 2,
                    "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "ESCAPE",
                    "card_value" => null,
                    "card_pirate" => null,
                    "card_color" => null,
                    "card_id" => "ESCAPE",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],
                [
                    "player_id" => 1,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Davy",
                    "card_type" => "ESCAPE",
                    "card_value" => null,
                    "card_pirate" => null,
                    "card_color" => null,
                    "card_id" => "ESCAPE",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],

            ]);

        $result5 = $fold5->resolve();
        $this->assertNull($result5->getCardInFold());
    }

    public function test_all_players_played_same_color_should_return_winner()
    {

        $expectedWinner = [
            "player_id" => 1,
            "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "player_name" => "Davy",
            "card_type" => "COLORED",
            "card_value" => "9",
            "card_pirate" => null,
            "card_color" => "BLACK",
            "card_id" => "BLACK_9",
            'prop_time' => (new DateTimeImmutable())->format('U.u')

        ];
        $fold5 = new Fold(
            [
                [
                    "player_id" => 2,
                    "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "COLORED",
                    "card_value" => "4",
                    "card_pirate" => null,
                    "card_color" => "BLACK",
                    "card_id" => "BLACK_4",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],
                $expectedWinner,

            ]);

        $result5 = $fold5->resolve();
        $this->assertEquals($result5, new FoldResolved(new CardInFold(
            $expectedWinner['card_id'],
            $expectedWinner['player_id'],
            $expectedWinner['prop_time'])));
    }

    public function test_first_color_played_is_the_color_asked()
    {

        $expectedWinner = [
            "player_id" => 1,
            "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "player_name" => "Davy",
            "card_type" => "COLORED",
            "card_value" => "9",
            "card_pirate" => null,
            "card_color" => "RED",
            "card_id" => "RED_9",
            'prop_time' => (new DateTimeImmutable())->sub(new DateInterval('PT2S'))->format('U.u')

        ];
        $fold5 = new Fold(
            [
                [
                    "player_id" => 2,
                    "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "COLORED",
                    "card_value" => "11",
                    "card_pirate" => null,
                    "card_color" => "BLUE",
                    "card_id" => "BLUE_11",
                    'prop_time' => (new DateTimeImmutable())->format('U.u')

                ],
                $expectedWinner,

            ]);

        $result5 = $fold5->resolve();
        $this->assertEquals($result5, new FoldResolved(new CardInFold(
            $expectedWinner['card_id'],
            $expectedWinner['player_id'],
            $expectedWinner['prop_time'])));
    }


}