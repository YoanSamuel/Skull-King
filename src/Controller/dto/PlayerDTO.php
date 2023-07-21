<?php

namespace App\Controller\dto;


use App\Entity\Player;
use Symfony\Component\Uid\Uuid;

class PlayerDTO
{


    public string $id;
    public Uuid $userId;
    public string $name;
    public ?int $announce;

    public function __construct(Player $player)
    {
        $this->name = $player->getName();
        $this->id = $player->getId();
        $this->userId = $player->getUserId();
        $this->announce = $player->getAnnounce();
    }

}