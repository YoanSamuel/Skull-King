<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PlayerAnnounce
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(type: 'integer')]
    private int $playerId;

    #[ORM\Column]
    private int $announced;

    #[ORM\Column(type: 'integer')]
    private int $done = 0;

    #[ORM\ManyToOne(inversedBy: 'playerAnnounces')]
    private FoldResult $foldResult;


    public function __construct(int $playerId, int $announced)
    {
        $this->playerId = $playerId;
        $this->announced = $announced;
    }

    public function incrementDone(): void
    {
        $this->done = $this->done + 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    public function setPlayerId(int $playerId): void
    {
        $this->playerId = $playerId;
    }

    public function getAnnounced(): int
    {
        return $this->announced;
    }

    public function setAnnounced(int $announced): void
    {
        $this->announced = $announced;
    }

    public function getDone(): int
    {
        return $this->done;
    }

    public function setDone(int $done): void
    {
        $this->done = $done;
    }

    public function getFoldResult(): FoldResult
    {
        return $this->foldResult;
    }

    public function setFoldResult(FoldResult $foldResult): void
    {
        $this->foldResult = $foldResult;
    }


}