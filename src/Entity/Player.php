<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity]
class Player
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: Card::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $cards;

    #[ORM\Column(nullable: true)]
    private ?int $announce;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SkullKing $skullKing;

    public function __construct(SkullKing $skullKing, GameRoomUser $user, Collection $cards, ?int $announce = null)
    {

        $this->userId = $user->getUserId();
        $this->name = $user->getUserName();
        $this->cards = new ArrayCollection();
        foreach ($cards as $card) {
            $this->cards->add($card->setPlayer($this));
        }
        $this->announce = $announce;
        $this->skullKing = $skullKing;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function setCards(Collection $cards)
    {
        $this->cards = $cards;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAnnounce(): int|null
    {
        return $this->announce;
    }

    public function setAnnounce($announce): static
    {
        $this->announce = $announce;
        return $this;
    }

    public function playCard(Card $card)
    {

        $this->cards->removeElement($card);
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