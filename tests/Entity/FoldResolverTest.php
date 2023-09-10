<?php

namespace App\Tests\Entity;

use App\Entity\CardInFold;
use App\Entity\Fold;
use App\Entity\FoldResolved;
use PHPUnit\Framework\TestCase;

class FoldResolverTest extends TestCase
{

    public function test_resolve_fold()
    {
        $fold = new Fold([
            1,
            2
        ],
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
                    "card_id" => "2_RED",
                ],
                [
                    "player_id" => 2,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Arthur",
                    "card_type" => "COLORED",
                    "card_value" => "11",
                    "card_pirate" => null,
                    "card_mermaid" => null,
                    "card_color" => "BLACK",
                    "card_id" => "11_BLACK",
                ]
            ]);

        $fold1 = new Fold(
            [
                1,
                2
            ], [
            [
                "player_id" => 1,
                "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "COLORED",
                "card_value" => "12",
                "card_pirate" => null,
                "card_mermaid" => null,
                "card_color" => "RED",
                "card_id" => "12_RED",
            ],
            [
                "player_id" => 2,
                "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Arthur",
                "card_type" => "COLORED",
                "card_value" => "11",
                "card_pirate" => null,
                "card_mermaid" => null,
                "card_color" => "BLUE",
                "card_id" => "11_BLUE",
            ]
        ]);

        $fold2 = new Fold(
            [
                1,
                2,
                3,
            ], [
            [
                "player_id" => 2,
                "player_userid" => "f3798fc3-e4c3-4482-b21d-10ae533056d7",
                "player_name" => "Davy",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "MONIQUE",
                "card_color" => null,
                "card_id" => "MONIQUE_MERMAID",
            ],
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
            ],

        ]);


        $fold3 = new Fold([
            1,
            2,
            3
        ], [
            [
                "player_id" => 1,
                "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "TORTUGAJACK",
                "card_color" => null,
                "card_id" => "TORTUGAJACK_PIRATE",
            ],
            [
                "player_id" => 2,
                "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "BETTYBRAVE",
                "card_color" => null,
                "card_id" => "BETTYBRAVE_PIRATE",
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
            ],

        ]);

        $fold4 = new Fold([
            1,
            2
        ], [
            [
                "player_id" => 1,
                "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "ELISABETH",
                "card_color" => null,
                "card_id" => "ELISABETH_MERMAID",
            ],
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
            ],

        ]);


        $result = $fold->resolve();
        $this->assertEquals($result, new FoldResolved(new CardInFold("11_BLACK", 2)));

        $result1 = $fold1->resolve();
        $this->assertEquals($result1, new FoldResolved(new CardInFold("12_RED", 1)));

        $result2 = $fold2->resolve();
        $this->assertEquals($result2, new FoldResolved(new CardInFold("MONIQUE_MERMAID", 2), 50));

        $result3 = $fold3->resolve();
        $this->assertEquals($result3, new FoldResolved(new CardInFold("TORTUGAJACK_PIRATE", 1)));

        $result4 = $fold4->resolve();
        $this->assertEquals($result4, new FoldResolved(new CardInFold("ELISABETH_MERMAID", 1)));


    }

    public function test_all_players_played_escape_should_return_null()
    {

        $fold5 = new Fold(["7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8"
        ],
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
                ],

            ]);

        $result5 = $fold5->resolve();
        $this->assertNull($result5->getCardInFold());
    }

    public function test_all_players_played_same_color_should_return_winner()
    {

        $fold5 = new Fold([
            1,
            2
        ],
            [
                [
                    "player_id" => 2,
                    "player_userid" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "COLORED",
                    "card_value" => "4",
                    "card_pirate" => null,
                    "card_color" => "BLACK",
                    "card_id" => "4_BLACK",
                ],
                [
                    "player_id" => 1,
                    "player_userid" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Davy",
                    "card_type" => "COLORED",
                    "card_value" => "9",
                    "card_pirate" => null,
                    "card_color" => "BLACK",
                    "card_id" => "9_BLACK",
                ],

            ]);

        $result5 = $fold5->resolve();
        $this->assertEquals($result5, new FoldResolved(new CardInFold("9_BLACK", 1)));
    }


}