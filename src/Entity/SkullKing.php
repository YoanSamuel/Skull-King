<?php

namespace App\Entity;

use App\Repository\SkullKingRepository;
use DateTimeImmutable;
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
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Version]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $version;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $nbRound = 1;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $currentPlayerId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $colorAsked = null;

    #[ORM\Column(name: 'jsonCards', type: 'json', nullable: true)]
    private array $fold = [];

    #[ORM\Column(nullable: false)]
    private string $state;

    #[ORM\OneToMany(mappedBy: 'skullKing', targetEntity: Player::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $players;


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

    public function findPlayer(Uuid $userId): Player|null
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
    public function playCard(Uuid $userId, string $cardId): array
    {

        $player = $this->findPlayer($userId);


        if (is_null($player)) {
            throw new Exception('Ce joueur nexiste pas Billy!');
        }

        if ($this->hasAlReadyPlayed($player)) {
            throw new Exception('Tu ne peux pas jouer deux fois dans le même tour!');
        }

        if ($player->getId() !== $this->currentPlayerId) {
            throw new Exception('Ce n\'est pas à toi de jouer, ' . $player->getName() . '!');
        }

        $card = $player->findCard($cardId);
        if (is_null($card)) {
            throw new Exception('Tu ne peux pas jouer une carte que tu ne possèdes pas , ' . $player->getName() . '!');
        }

        $isCardPlayedColored = $card->getCardType() == CardType::COLORED->value;
        $isColorAsked = !is_null($this->colorAsked);
        $isCardPlayedSameColor = $card->getColor() == $this->colorAsked;
        $hasTheColorAsked = $player->hasTheColorAsked($this->colorAsked);

        if ($isColorAsked && $isCardPlayedColored && !$isCardPlayedSameColor && !$hasTheColorAsked) {
            throw new Exception('Tu TRICHEEEEEEEEEEEEEEEEEEEEES ' . $player->getName() . '!');
        }

        $this->addCardInFold($player, $card);

        $this->currentPlayerId = $this->nextPlayerId($this->currentPlayerId);

        $cardsPlayedCount = count($this->fold);
        $allPlayersPlayed = $cardsPlayedCount === count($this->players);

        $player->removeCardPlayed($card);

        if ($allPlayersPlayed) {

            $winner = $this->resolveFold();
            if (!is_null($winner)) {
                $this->currentPlayerId = $winner->getId();
            }
            $this->fold = [];

            $everyPlayersHasEmptyHand = $this->players->forAll(function (int $key, Player $player) {
                return count($player->getCards()) == 0;
            });

            if ($everyPlayersHasEmptyHand) {
                $this->prepareNextRound();
            }

        }
        return $this->fold;

    }

    public function hasAlReadyPlayed(Player $player): bool
    {

        foreach ($this->fold as $cardPlayed) {
            if ($cardPlayed['player_id'] == $player->getId()) {
                return true;
            }
        }
        return false;
    }

    public function resolveFold(): ?Player
    {
        $foldSortedByPlayerId = $this->getSortedFoldByPlayerId();

        $foldToResolve = new Fold($foldSortedByPlayerId, $this->fold);
        $cardInFold = $foldToResolve->resolve();
        if (is_null($cardInFold)) {
            return null;
        }
        return $this->findPlayer($cardInFold->getPlayerId());

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
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

    private function prepareNextRound(): void
    {
        //set score in players
        // creer fonction resolve_score

        $this->nbRound += 1;
        $this->state = SkullKingPhase::ANNOUNCE->value;
        $deck = new Deck();
        $deck->shuffle();
        /** @var Player $player */
        foreach ($this->players as $player) {
            $player->setAnnounce(null);

            $cardsId = array_map(function (Card $card) {
                return $card->getId();
            }, $deck->distribute($this->nbRound));
            $player->setCards($cardsId);

        }

    }

    private function nextPlayerId(int $targetPlayerId): int
    {

        $targetPlayerIdIndex = null;
        $sortedPlayers = $this->getPlayersSortedById();
        foreach ($sortedPlayers as $index => $player) {
            if ($player->getId() == $targetPlayerId) {
                $targetPlayerIdIndex = $index;
            }
        }

        $maxIndex = count($sortedPlayers) - 1;
        if ($targetPlayerIdIndex == $maxIndex) {
            $nextPlayerIdIndex = 0;
        } else {
            $nextPlayerIdIndex = $targetPlayerIdIndex + 1;
        }

        return $sortedPlayers[$nextPlayerIdIndex]->getId();
    }

    /**
     * @param Player $player
     * @param Card $card
     * @return void
     */
    public function addCardInFold(Player $player, Card $card): void
    {
        $this->fold[] = array(
            'player_id' => $player->getUserId()->toRfc4122(),
            'player_name' => $player->getName(),
            'card_type' => $card->getCardType(),
            'card_value' => $card->getValue(),
            'card_pirate' => $card->getPirateName(),
            'card_mermaid' => $card->getMermaidName(),
            'card_color' => $card->getColor(),
            'card_id' => $card->getId(),
        );
    }

    /**
     * @return int[]|null[]
     */
    public function getSortedFoldByPlayerId(): array
    {
        $id = $this->currentPlayerId;
        $foldSortedByPlayerId = [$this->currentPlayerId];
        while ($id != $this->currentPlayerId) {

            $id = $this->nextPlayerId($id);
            $foldSortedByPlayerId[] = $id;
        }
        return $foldSortedByPlayerId;
    }


}