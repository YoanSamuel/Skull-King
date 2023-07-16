<?php

namespace App\Controller\dto;

use App\Entity\GameRoomUser;

class UserDTO
{
    public string $userName;
    public string $userId;

    public function __construct(GameRoomUser $user)
    {
        $this->userName = $user->getUserName();
        $this->userId = $user->getUserId();
    }
}