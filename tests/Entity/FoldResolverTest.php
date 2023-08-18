<?php

namespace App\Tests\Entity;

use App\Entity\CardInFold;
use App\Entity\Fold;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class FoldResolverTest extends TestCase
{

    public function test_resolve_fold()
    {
        $fold = new Fold([
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "7456109d-755b-40c2-9ef7-9fda8ff60abe"
        ],
            [
                [
                    "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "COLORED",
                    "card_value" => "2",
                    "card_pirate" => null,
                    "card_color" => "RED",
                    "card_id" => "2_RED",
                ],
                [
                    "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Arthur",
                    "card_type" => "COLORED",
                    "card_value" => "11",
                    "card_pirate" => null,
                    "card_color" => "BLACK",
                    "card_id" => "11_BLACK",
                ]
            ]);

        $fold1 = new Fold(
            [
                "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "7456109d-755b-40c2-9ef7-9fda8ff60abe"
            ], [
            [
                "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "COLORED",
                "card_value" => "12",
                "card_pirate" => null,
                "card_color" => "RED",
                "card_id" => "12_RED",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Arthur",
                "card_type" => "COLORED",
                "card_value" => "11",
                "card_pirate" => null,
                "card_color" => "BLUE",
                "card_id" => "11_BLUE",
            ]
        ]);

        $fold2 = new Fold(
            [
                "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "f3798fc3-e4c3-4482-b21d-10ae533056d7",
                "7456109d-755b-40c2-9ef7-9fda8ff60a7e",
            ], [
            [
                "player_id" => "f3798fc3-e4c3-4482-b21d-10ae533056d7",
                "player_name" => "Davy",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "MONIQUE",
                "card_color" => null,
                "card_id" => "MONIQUE_MERMAID",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60a7e",
                "player_name" => "Arthur",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "HARRYTHEGIANT",
                "card_color" => null,
                "card_id" => "HARRYTHEGIANT_PIRATE",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "SKULLKING",
                "card_value" => null,
                "card_pirate" => null,
                "card_color" => null,
                "card_id" => "SKULLKING",
            ],

        ]);


        $fold3 = new Fold([
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "7456109d-755b-40c2-9ef7-9fda8ff60a7e"
        ], [
            [
                "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "TORTUGAJACK",
                "card_color" => null,
                "card_id" => "TORTUGAJACK_PIRATE",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "BETTYBRAVE",
                "card_color" => null,
                "card_id" => "BETTYBRAVE_PIRATE",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60a7e",
                "player_name" => "Arthur",
                "card_type" => "PIRATE",
                "card_value" => null,
                "card_pirate" => "HARRYTHEGIANT",
                "card_color" => null,
                "card_id" => "HARRYTHEGIANT_PIRATE",
            ],

        ]);

        $fold4 = new Fold([
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "7456109d-755b-40c2-9ef7-9fda8ff60abe"
        ], [
            [
                "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "ELISABETH",
                "card_color" => null,
                "card_id" => "ELISABETH_MERMAID",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "MERMAID",
                "card_value" => null,
                "card_pirate" => null,
                "card_mermaid" => "MONIQUE",
                "card_color" => null,
                "card_id" => "MONIQUE_MERMAID",
            ],

        ]);

        $fold5 = new Fold([
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
            "7456109d-755b-40c2-9ef7-9fda8ff60abe"
        ], [
            [
                "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                "player_name" => "Morgan",
                "card_type" => "ESCAPE",
                "card_value" => null,
                "card_pirate" => null,
                "card_color" => null,
                "card_id" => "ESCAPE",
            ],
            [
                "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                "player_name" => "Davy",
                "card_type" => "ESCAPE",
                "card_value" => null,
                "card_pirate" => null,
                "card_color" => null,
                "card_id" => "ESCAPE",
            ],

        ]);


        $result = $fold->resolve();
        $this->assertEquals($result, new CardInFold("11_BLACK", new Uuid("7456109d-755b-40c2-9ef7-9fda8ff60abe")));

        $result1 = $fold1->resolve();
        $this->assertEquals($result1, new CardInFold("12_RED", new Uuid("14b44a8a-b322-415c-9a2c-72e9d139ecd8")));

        $result2 = $fold2->resolve();
        $this->assertEquals($result2, new CardInFold("MONIQUE_MERMAID", new Uuid("f3798fc3-e4c3-4482-b21d-10ae533056d7")));

        $result3 = $fold3->resolve();
        $this->assertEquals($result3, new CardInFold("TORTUGAJACK_PIRATE", new Uuid("14b44a8a-b322-415c-9a2c-72e9d139ecd8")));

        $result4 = $fold4->resolve();
        $this->assertEquals($result4, new CardInFold("ELISABETH_MERMAID", new Uuid("14b44a8a-b322-415c-9a2c-72e9d139ecd8")));


    }

    public function test_all_players_played_escape_should_return_null()
    {

        $fold5 = new Fold(["7456109d-755b-40c2-9ef7-9fda8ff60abe",
            "14b44a8a-b322-415c-9a2c-72e9d139ecd8"
        ],
            [
                [
                    "player_id" => "14b44a8a-b322-415c-9a2c-72e9d139ecd8",
                    "player_name" => "Morgan",
                    "card_type" => "ESCAPE",
                    "card_value" => null,
                    "card_pirate" => null,
                    "card_color" => null,
                    "card_id" => "ESCAPE",
                ],
                [
                    "player_id" => "7456109d-755b-40c2-9ef7-9fda8ff60abe",
                    "player_name" => "Davy",
                    "card_type" => "ESCAPE",
                    "card_value" => null,
                    "card_pirate" => null,
                    "card_color" => null,
                    "card_id" => "ESCAPE",
                ],

            ]);

        $result5 = $fold5->resolve();
        $this->assertEquals($result5, null);
    }

}