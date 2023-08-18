<?php

namespace App\Entity;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping as ORM;



class Card
{

    private string $cardType;

    private ?string $pirateName;

    private ?string $color;

    private ?string $value;

    private ?Player $player = null;

    private $mermaidName;


    /**
     * @param string $cardType
     * @param string|null $pirateName
     * @param string|null $color
     * @param string|null $value
     */
    public function __construct(string $cardType, ?string $pirateName, ?string $mermaidName, ?string $color, ?string $value)
    {
        $this->cardType = $cardType;
        $this->pirateName = $pirateName;
        $this->mermaidName = $mermaidName;
        $this->color = $color;
        $this->value = $value;
    }

    public static function coloredCard(CardColor $color, int $value): Card
    {
        return new Card( CardType::COLORED->value, null, null, $color->value, $value);
    }

    public static function pirateCard(PirateName $name): Card
    {
        return new Card( CardType::PIRATE->value, $name->value, null, null, null);
    }

    public static function skullCard(): Card
    {
        return new Card( CardType::SKULLKING->value, null, null, null, null);
    }

    public static function escapeCard(): Card
    {
        return new Card( CardType::ESCAPE->value, null, null, null, null);
    }

    public static function scaryMaryCard(): Card
    {
        return new Card( CardType::SCARYMARY->value, null, null, null, null);
    }

    public static function mermaidCard(MermaidName $name): Card
    {
        return new Card( CardType::MERMAID->value, null, $name->value, null, null);
    }

    public static function create(string $cardId): Card
    {

        $splitId = explode('_', $cardId);
        return match ($cardId) {
            CardType::SKULLKING->value =>  Card::skullCard(),
            CardType::ESCAPE->value =>  Card::escapeCard(),
            CardType::SCARYMARY->value =>  Card::scaryMaryCard(),
            default => str_contains($cardId, CardType::PIRATE->value)
                        ? Card::pirateCard(PirateName::from($splitId[0]))
                            : (str_contains($cardId, CardType::MERMAID->value)
                        ? Card::mermaidCard(MermaidName::from($splitId[0]))
                            : Card::coloredCard(CardColor::from($splitId[1]), $splitId[0]))

        };
    }

    public function getId(): string
    {
        return match ($this->cardType) {
            CardType::COLORED->value => $this->value . "_" . $this->color,
            CardType::PIRATE->value => $this->pirateName. "_PIRATE",
            CardType::MERMAID->value => $this->mermaidName. "_MERMAID",
            default => $this->cardType,
        };
    }

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
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getMermaidName() : ?string
    {
        return $this->mermaidName;
    }

}