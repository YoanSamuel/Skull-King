<?php

namespace App\Service;

interface CardPlay
{
    public function Play(array $cards, string $card): array;
}