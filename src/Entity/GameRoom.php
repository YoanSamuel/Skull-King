<?php

namespace App\Entity;

use App\Repository\GameRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameRoomRepository::class)]
class GameRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: GameRoomUser::class, cascade: ["persist"], fetch: 'EAGER')]
    private Collection $users;

    private bool $containsCurrentUser = false;

    #[ORM\JoinColumn(name: 'skull_king_id', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: SkullKing::class, cascade: ['persist', 'remove'])]
    private ?SkullKing $skullKing = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, GameRoomUser>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(GameRoomUser $user): self
    {
        if (!$this->hasUser($user->getUserId())) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(GameRoomUser $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }


    public function isContainsCurrentUser(): bool
    {
        return $this->containsCurrentUser;
    }


    public function setContainsCurrentUser(Uuid $currentUserId): void
    {
        $this->containsCurrentUser = $this->hasUser($currentUserId);
    }

    private function hasUser(Uuid $userId): bool
    {
        foreach ($this->getUsers() as $gameRoomUser) {
            if ($gameRoomUser->getUserId()->equals($userId)) {
                return true;
            }

        }
        return false;
    }

    public function getSkullKing(): ?SkullKing
    {
        return $this->skullKing;
    }

    public function setSkullKing(?SkullKing $skullKing): self
    {
        $this->skullKing = $skullKing;

        return $this;
    }
}
