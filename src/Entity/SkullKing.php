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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $currentPlayerId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $colorAsked;

    #[ORM\Column(type: 'json', nullable:true)]
    private ?array $jsonCards;

    #[ORM\Column(nullable: false)]
    private string $state;

    #[ORM\OneToMany(mappedBy: 'skullKing', targetEntity: Player::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $players;

    private array $fold = [];


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

            $this->players->add(new Player($this, $user, $deck->distribute($this->nbRound), null));

        }
        $this->fold = [];
        $this->state = SkullKingPhase::ANNOUNCE->value;
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
            $sortedPlayers = $this->getPlayersSortedById();
            $this->currentPlayerId = $sortedPlayers[0]->getId();
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

    public function getPlayers(): Collection
    {
        return $this->players;
    }


    public function getFold(): array
    {
        return $this->fold;
    }

    /**
     * @throws Exception
     */
    public function playCard(Uuid $userId, Card $card): array
    {

        $player = $this->findPlayer($userId);


        if (is_null($player)) {
            throw new \Exception('Ce joueur nexiste pas Billy!');
        }

        $playerHasAlReadyPlayed = !is_null($this->getCardInFoldFor($player));
        if ($playerHasAlReadyPlayed) {
            throw new \Exception('Tu ne peux pas jouer deux fois dans le même tour!');
        }

        if ($player->getId() !== $this->currentPlayerId) {
            throw new \Exception('Ce n\'est pas à toi de jouer, ' . $player->getName() . '!');
        }

        if (!$player->getCards()->contains($card)) {
            throw new \Exception('Tu ne peux pas jouer une carte que tu ne possèdes pas , ' . $player->getName() . '!');
        }

        $isCardPlayedColored = $card->getCardType() == CardType::COLORED->value;
        $isColorAsked = !is_null($this->colorAsked);
        $isCardPlayedSameColor = $card->getColor() == $this->colorAsked;
        $hasTheColorAsked = $player->getCards()->findFirst(function (int $key, Card $card) {
            return $card->getColor() == $this->colorAsked;
        });

        if ($isColorAsked && $isCardPlayedColored && !$isCardPlayedSameColor && !$hasTheColorAsked) {
            throw new \Exception('Tu TRICHEEEEEEEEEEEEEEEEEEEEES ' . $player->getName() . '!');
        }

        $card->setSkullKing($this);
        $this->setFold($this->addCardInFold($player, $card));

        $this->currentPlayerId = $this->nextPlayerId();

        $cardsPlayedCount = count($this->fold);
        $allPlayersPlayed = $cardsPlayedCount === count($this->players);


        if ($allPlayersPlayed) {

            $winner = $this->resolveFold($this->fold);

            $this->players = $this->players->map(function (Player $player) {
                $player->removeCardPlayed($this->fold);
                return $player;
            });
            $this->fold= [];

            $everyPlayersHasEmptyHand = $this->players->forAll(function (int $key, Player $player) {
                return $player->getCards()->count() == 0;
            });

            if ($everyPlayersHasEmptyHand) {
                $this->prepareNextRound();
            }

        }

        return $this->fold;

    }

    public function getCardInFoldFor(Player $player): ?Card
    {
        $cardInFold = $player->getCards()->findFirst(function (int $key, Card $card) {
            return $card;
        });
        foreach($this->fold as $cardPlayed) {
            if($cardPlayed['card_id'] == $cardInFold->getId())
            {
                return $cardInFold;
            }
        }
        return null;
    }

    public function resolveFold(array $fold): Player
    {
        $winningCard = null;
        $winningPlayer = null;

        return $this->players[0];
    }

    public function getPlayersSortedById(): array
    {

        $playersArray = $this->players->toArray();

        // Tri du tableau de joueurs par ID
        usort($playersArray, function (Player $player1, Player $player2) {
            return $player1->getId() <=> $player2->getId();
        });

        return $playersArray;
    }

    /**
     * @param array $fold
     */
    public function setFold(array $fold): void
    {
        $this->fold = $fold;
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

    public function getCurrentPlayerId(): int
    {
        return $this->currentPlayerId;
    }

    public function setCurrentPlayerId(int $currentPlayerId): void
    {
        $this->currentPlayerId = $currentPlayerId;
    }

    private function prepareNextRound()
    {
        //set score in players
        // creer fonction resolve_score

        $this->nbRound += 1;
        $this->state = SkullKingPhase::ANNOUNCE->value;

        $deck = new Deck();
        foreach ($this->players as $player) {
            $player->setAnnounce(null);
            $player->setCards($deck->distribute($this->nbRound));

        }

    }

    private function nextPlayerId(): int
    {
        $nextPlayerIdIndex = null;
        $currentPlayerIdIndex = null;
        $sortedPlayers = $this->getPlayersSortedById();
        foreach ($sortedPlayers as $index => $player) {
            if ($player->getId() == $this->currentPlayerId) {
                $currentPlayerIdIndex = $index;
            }
        }

        $maxIndex = count($sortedPlayers) - 1;
        if ($currentPlayerIdIndex == $maxIndex) {
            $nextPlayerIdIndex = 0;
        } else {
            $nextPlayerIdIndex = $currentPlayerIdIndex + 1;
        }

        return $sortedPlayers[$nextPlayerIdIndex]->getId();
    }

    public function getJsonCards(): ?array
    {
        return $this->jsonCards;
    }

    /**
     * @param Player $player
     * @param Card $card
     * @return void
     */
    public function addCardInFold(Player $player, Card $card): array
    {
        $this->fold[] = [
            'player_id' => $player->getUserId(),
            'player_name' => $player->getName(),
            'card_type' => $card->getCardType(),
            'card_value' => $card->getValue(),
            'card_pirate' => $card->getPirateName(),
            'card_color' => $card->getColor(),
            'card_id' => $card->getId(),
        ];
        return $this->fold;
    }
}