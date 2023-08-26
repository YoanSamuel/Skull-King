<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FoldResult
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $nbRound;

    #[ORM\OneToMany(mappedBy: 'foldResult', targetEntity: PlayerAnnounce::class, cascade: ['persist', 'remove'], fetch: "EAGER", orphanRemoval: true)]
    private Collection $playerAnnounces;


    #[ORM\ManyToOne(inversedBy: 'foldResults')]
    private SkullKing $skullKing;

    public static function announce(SkullKing $skullKing): FoldResult
    {
        $foldResult = new FoldResult();
        $foldResult->setNbRound($skullKing->getNbRound());
        $foldResult->setSkullKing($skullKing);
        $players = $skullKing->getPlayers();
        $playersAnnounces = new ArrayCollection();
        /** @var Player $player */
        foreach ($players as $player) {

            $announce = new PlayerAnnounce(
                $player->getId(),
                $player->getAnnounce()
            );
            $announce->setFoldResult($foldResult);
            $playersAnnounces->add($announce);
        }
        $foldResult->setPlayerAnnounces($playersAnnounces);
        return $foldResult;
    }

    public function incrementFoldDone(Player $winner): void
    {

        /**
         * @var PlayerAnnounce $winnerPlayerAnnounce
         */

        $winnerPlayerAnnounce = $this->getPlayerAnnounces()->findFirst(function (int $key, PlayerAnnounce $playerAnnounce) use ($winner) {
            return $winner->getId() == $playerAnnounce->getPlayerId();
        });
        $winnerPlayerAnnounce->incrementDone();
    }

    public function getNbRound(): int
    {
        return $this->nbRound;
    }

    public function setNbRound(int $nbRound): void
    {
        $this->nbRound = $nbRound;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSkullKing(): SkullKing
    {
        return $this->skullKing;
    }

    public function setSkullKing(SkullKing $skullKing): void
    {
        $this->skullKing = $skullKing;
    }

    public function getPlayerAnnounces(): Collection
    {
        return $this->playerAnnounces;
    }

    public function setPlayerAnnounces(Collection $playerAnnounces): void
    {
        $this->playerAnnounces = $playerAnnounces;
    }


}