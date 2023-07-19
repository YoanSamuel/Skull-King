<?php

namespace App\Entity;

use App\Repository\SkullKingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SkullKingRepository::class)]
class SkullKing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Version]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $version;

    private int $nbRound = 1;

    #[ORM\Column(nullable: false)]
    private string $state;

    #[ORM\OneToMany(mappedBy: 'skullKing', targetEntity: Player::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'skullKing', targetEntity: Card::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $fold;


    /**
     * @throws Exception
     */
    public function __construct(Collection $users, $deck = new Deck())
    {

        if (count($users) < 2) {
            throw new Exception('Il n\'y a pas assez de joueurs dans cette partie.');
        }

        $this->players = new ArrayCollection();
        foreach ($users as $user) {

            $this->players->add(new Player($this, $user, new ArrayCollection([$deck->pop()]), null));

        }
        $this->fold = new ArrayCollection();
        $this->state = SkullKingPhase::ANNOUNCE->value;
        $this->version = 1;

    }

    /**
     * @return int
     */
    public function getNbRound(): int
    {
        return $this->nbRound;
    }

    public function announce(Uuid $userId, int $announce): void
    {
        $count = 0;
        $player = $this->findPlayer($userId);
        $player->setAnnounce($announce);

        foreach ($this->players as $playerInGame) {
            if ($playerInGame->getAnnounce() !== null) {
                $count++;
            }
        }

        if ($count == count($this->players)) {
            $this->state = SkullKingPhase::PLAYCARD->value;
        }
    }

    public function getAnnouncePerPlayer(Uuid $userId): int
    {
        $player = $this->findPlayer($userId);
        return $player->getAnnounce();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param Uuid $userId
     * @return Player|mixed|null
     */
    public function findPlayer(Uuid $userId): mixed
    {
        return $this->players->findFirst(
            function (int $key, Player $player) use ($userId) {
                return $player->getUserId()->equals($userId);
            });
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getFold(): Collection
    {
        return $this->fold;
    }


    public function addToFold(Uuid $userId, Card $card): void
    {
        $player = $this->findPlayer($userId);
        $this->fold->add($card);
        $player->playCard($card);
    }

    /**
     * @param Collection $fold
     */
    public function setFold(Collection $fold): void
    {
        $this->fold = $fold;
    }

    public function isGamePhase(): void
    {
        $this->state = SkullKingPhase::PLAYCARD->value;
    }

    public function isAnnouncePhase(): void
    {
        $this->state = SkullKingPhase::ANNOUNCE->value;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion($version): self
    {
        $this->version = $version;
        return $this;
    }


}