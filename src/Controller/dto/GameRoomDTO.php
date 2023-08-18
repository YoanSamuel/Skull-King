<?php

namespace App\Controller\dto;

use App\Entity\GameRoom;

class GameRoomDTO
{
    public string $id;
    public ?\DateTimeImmutable $createdAt;

    public function __construct(GameRoom $gameRoom)
    {
        $this->id = $gameRoom->getId();
        $this->createdAt = $gameRoom->getCreatedAt();
    }
}