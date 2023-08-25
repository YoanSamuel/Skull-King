<?php

namespace App\Entity;


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

    #[ORM\Column(name: 'cards', type: 'json')]
    private array $cards = [];

    #[ORM\Column(nullable: true)]
    private ?int $announce;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SkullKing $skullKing;

    #[ORM\Column(name: 'score', type: 'decimal')]
    private int $score;

    public function __construct(SkullKing $skullKing, GameRoomUser $user, array $cards, ?int $announce = null, int $score = 0)
    {

        $this->userId = $user->getUserId();
        $this->name = $user->getUserName();
        foreach ($cards as $card) {
            $this->cards[] = $card->getId();
        }
        $this->announce = $announce;
        $this->skullKing = $skullKing;
        $this->score = $score;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function findCard(string $cardId): ?Card
    {
        if (in_array($cardId, $this->cards)) {
            return Card::create($cardId);
        }
        return null;
    }

    public function setCards(array $cards): void
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


    public function getSkullKing(): ?SkullKing
    {
        return $this->skullKing;
    }

    public function setSkullKing(?SkullKing $skullKing): self
    {
        $this->skullKing = $skullKing;

        return $this;
    }

    public function removeCardPlayed(Card $card): void
    {
        $card->setPlayer(null);
        $index = array_search($card->getId(), $this->cards);
        unset($this->cards[$index]);
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function hasTheColorAsked(?string $colorAsked): bool
    {
        if (is_null($colorAsked)) {
            return false;
        }

        foreach ($this->cards as $cardId) {
            $card = Card::create($cardId);
            if ($card->getColor() == $colorAsked) {
                return true;
            }
        }
        return false;
    }

    public function incrementScore(int $pointsWin): void
    {
        $this->score += $pointsWin;
    }

}