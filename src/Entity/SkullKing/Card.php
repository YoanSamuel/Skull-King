<?php

namespace App\Entity\SkullKing;

class Card
{
    private array $cards = [
        '1_yellow', '1_purple', '1_blue', '1_black',
        '2_yellow', '2_purple', '2_blue', '2_black',
        '3_yellow', '3_purple', '3_blue', '3_black',
        '4_yellow', '4_purple', '4_blue', '4_black',
        '5_yellow', '5_purple', '5_blue', '5_black',
        '6_yellow', '6_purple', '6_blue', '6_black',
        '7_yellow', '7_purple', '7_blue', '7_black',
        '8_yellow', '8_purple', '8_blue', '8_black',
        '9_yellow', '9_purple', '9_blue', '9_black',
        '10_yellow', '10_purple', '10_blue', '10_black',
        '11_yellow', '11_purple', '11_blue', '11_black',
        '12_yellow', '12_purple', '12_blue', '12_black',
        '13_yellow', '13_purple', '13_blue', '13_black',
        '14_yellow', '14_purple', '14_blue', '14_black',

    ];


    public function getCards(): array
    {
        return $this->cards;
    }

    public function getRandomCard() : string
    {
        $cards = $this->getCards();
        $index = array_rand($cards);
        return  $cards[$index];
    }




}