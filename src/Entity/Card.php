<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column]
    private string $cardType;

    #[ORM\Column(nullable: true)]
    private ?string $pirateName;

    #[ORM\Column(nullable: true)]
    private ?string $color;

    #[ORM\Column(nullable: true)]
    private ?string $value;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Player $player = null;


    #[ORM\ManyToOne(inversedBy: 'fold')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SkullKing $skullKing = null;

    /**
     * @param int|null $id
     * @param string $cardType
     * @param string|null $pirateName
     * @param string|null $color
     * @param string|null $value
     */
    public function __construct(?int $id, string $cardType, ?string $pirateName, ?string $color, ?string $value)
    {
        $this->id = $id;
        $this->cardType = $cardType;
        $this->pirateName = $pirateName;
        $this->color = $color;
        $this->value = $value;
    }

    public static function coloredCard(CardColor $color, int $value): Card
    {
        return new Card(null, CardType::COLORED->value, null, $color->value, $value);
    }

    public static function pirateCard(PirateName $name): Card
    {
        return new Card(null, CardType::PIRATE->value, $name->value, null, null);
    }

    public static function skullCard(): Card
    {
        return new Card(null, CardType::SKULLKING->value, null, null, null);
    }

    public static function escapeCard(): Card
    {
        return new Card(null, CardType::ESCAPE->value, null, null, null);
    }

    public static function scaryMaryCard(): Card
    {
        return new Card(null, CardType::SCARYMARY->value, null, null, null);
    }

    public static function mermaidCard(): Card
    {
        return new Card(null, CardType::MERMAID->value, null, null, null);
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCardType(): string
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     */
    public function setCardType(string $cardType): void
    {
        $this->cardType = $cardType;
    }

    /**
     * @return string|null
     */
    public function getPirateName(): ?string
    {
        return $this->pirateName;
    }

    /**
     * @param string|null $pirateName
     */
    public function setPirateName(?string $pirateName): void
    {
        $this->pirateName = $pirateName;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @param Player|null $player
     * @return Card
     */
    public function setPlayer(?Player $player): Card
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @param SkullKing|null $skullKing
     */
    public function setSkullKing(?SkullKing $skullKing): void
    {
        $this->skullKing = $skullKing;
    }

    /**
     * @return SkullKing|null
     */
    public function getSkullKing(): ?SkullKing
    {
        return $this->skullKing;
    }

    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getPower(): int
    {
        $cardType = $this->getCardType();

        return CARD_POWER_ORDER[$cardType];
    }

}