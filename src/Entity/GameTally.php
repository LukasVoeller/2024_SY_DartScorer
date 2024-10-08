<?php declare(strict_types=1);

/*
 * The Software is licensed, not sold. You may use the Software only as
 * permitted under the terms of the license agreement. Unauthorized use,
 * modification, distribution, or copying of the Software is strictly prohibited.
 *
 * @copyright  Copyright (c) Lukas Völler.
 * @author     Lukas Völler
 * @license    Proprietary License
 * @link       https://www.vllr.lu
 */

namespace App\Entity;

use App\Repository\GameTallyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GameTallyRepository::class)]
class GameTally
{
    // TODO: Add to_throw

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_tally_plain'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tally')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tally'])]
    private ?Game $game = null;

    #[ORM\Column]
    #[Groups(['api_tally_plain'])]
    private ?int $playerId = null;

    #[ORM\Column]
    #[Groups(['api_tally_plain'])]
    private ?int $score = null;

    #[ORM\Column]
    #[Groups(['api_tally_plain'])]
    private ?int $legId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_tally_plain'])]
    private ?int $setId = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_tally_plain'])]
    private ?int $legsWon = 0;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_tally_plain'])]
    private ?int $setsWon = 0;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_tally_plain'])]
    private ?bool $startedLeg = false;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_tally_plain'])]
    private ?bool $startedSet = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_tally_plain'])]
    private ?bool $toThrow = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }

    public function setPlayerId(int $playerId): static
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getLegId(): ?int
    {
        return $this->legId;
    }

    public function setLegId(int $legId): static
    {
        $this->legId = $legId;

        return $this;
    }

    public function getSetId(): ?int
    {
        return $this->setId;
    }

    public function setSetId(?int $setId): static
    {
        $this->setId = $setId;

        return $this;
    }

    public function getLegsWon(): ?int
    {
        return $this->legsWon;
    }

    public function setLegsWon(?int $legsWon): static
    {
        $this->legsWon = $legsWon;

        return $this;
    }

    public function getSetsWon(): ?int
    {
        return $this->setsWon;
    }

    public function setSetsWon(?int $setsWon): static
    {
        $this->setsWon = $setsWon;

        return $this;
    }

    public function getStartedLeg(): ?bool
    {
        return $this->startedLeg;
    }

    public function setStartedLeg(bool $startedLeg): static
    {
        $this->startedLeg = $startedLeg;

        return $this;
    }

    public function getStartedSet(): ?bool
    {
        return $this->startedSet;
    }

    public function setStartedSet(bool $startedSet): static
    {
        $this->startedSet = $startedSet;

        return $this;
    }

    public function getToThrow(): ?bool
    {
        return $this->toThrow;
    }

    public function setToThrow(?bool $toThrow): static
    {
        $this->toThrow = $toThrow;

        return $this;
    }
}
