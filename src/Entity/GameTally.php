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
    #[Groups(['tally'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tally')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tally'])]
    private ?Game $game = null;

    #[ORM\Column]
    #[Groups(['tally'])]
    private ?int $playerId = null;

    #[ORM\Column]
    #[Groups(['tally'])]
    private ?int $score = null;

    #[ORM\Column]
    #[Groups(['tally'])]
    private ?int $legId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tally'])]
    private ?int $setId = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['tally'])]
    private ?int $legsWon = 0;

    #[ORM\Column(nullable: false)]
    #[Groups(['tally'])]
    private ?int $setsWon = 0;

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
}
