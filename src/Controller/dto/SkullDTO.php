<?php

namespace App\Controller\dto;

use App\Entity\SkullKing;

class SkullDTO
{
    public string $id;
    public ?array $fold;

    public function __construct(SkullKing $skullKing)
    {
        $this->id = $skullKing->getId();
        $this->fold = $skullKing->getFold()->toArray();
    }
}