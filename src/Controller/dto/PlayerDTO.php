<?php

namespace App\Controller\dto;


use App\Entity\Player;
use Symfony\Component\Uid\Uuid;

class PlayerDTO
{

    public string $id;
    public string $userId;
    public string $name;
    public ?int $announce;
    public array $cards = [];

    public function __construct(Player $player, Uuid $currentUserId)
    {
        $this->name = $player->getName();
        $this->id = $player->getId();
        $this->userId = $player->getUserId()->toRfc4122();
        $this->announce = $player->getAnnounce();
        if ($player->getUserId()->equals($currentUserId)) {
            $this->cards = array_values($player->getCards()->map(fn($card) => new CardDTO($card, $player->getId())
            )->toArray());
        }

    }

}