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

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

//#[ORM\MappedSuperclass]
//#[ORM\Entity]
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "game_mode", type: "string")]
#[ORM\DiscriminatorMap([
    "X01" => "GameTypeX01",
    "Cricket" => "GameTypeCricket",
    "Shanghai" => "GameTypeShanghai",
])]
abstract class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $player1Id;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $player2Id;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $startingPlayerId;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $winnerPlayerId = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['game'])]
    private ?string $state = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['game'])]
    private ?string $type = null;

    #[ORM\Column(length: 32, nullable: false)]
    #[Groups(['game'])]
    private ?string $matchMode;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $matchModeSetsNeeded = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $matchModeLegsNeeded = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $toThrowPlayerId = null;

    /**
     * @var Collection<int, GameSet>
     */
    #[ORM\OneToMany(targetEntity: GameSet::class, mappedBy: 'relatedGame', orphanRemoval: true)]
    #[Groups(['game'])]
    private Collection $sets;

    /**
     * @var Collection<int, GameLeg>
     */
    #[ORM\OneToMany(targetEntity: GameLeg::class, mappedBy: 'relatedGame', orphanRemoval: true)]
    #[Groups(['game'])]
    private Collection $legs;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['game'])]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, GameTally>
     */
    #[ORM\OneToMany(targetEntity: GameTally::class, mappedBy: 'gameId', orphanRemoval: true)]
    private Collection $tally;

    public function __construct()
    {
        $this->sets = new ArrayCollection();
        $this->legs = new ArrayCollection();
        $this->date = new \DateTime();
        $this->tally = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPlayer1Id(): ?int
    {
        return $this->player1Id;
    }

    public function setPlayer1Id(int $player1Id): static
    {
        $this->player1Id = $player1Id;

        return $this;
    }

    public function getPlayer2Id(): ?int
    {
        return $this->player2Id;
    }

    public function setPlayer2Id(int $player2Id): static
    {
        $this->player2Id = $player2Id;

        return $this;
    }

    public function getStartingPlayerId(): ?int
    {
        return $this->startingPlayerId;
    }

    public function setStartingPlayerId(int $startingPlayerId): static
    {
        $this->startingPlayerId = $startingPlayerId;

        return $this;
    }

    public function getWinnerPlayerId(): ?int
    {
        return $this->winnerPlayerId;
    }

    public function setWinnerPlayerId(?int $winnerPlayerId): static
    {
        $this->winnerPlayerId = $winnerPlayerId;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMatchMode(): ?string
    {
        return $this->matchMode;
    }

    public function setMatchMode(string $matchMode): static
    {
        $this->matchMode = $matchMode;

        return $this;
    }

    public function getMatchModeSetsNeeded(): ?int
    {
        return $this->matchModeSetsNeeded;
    }

    public function setMatchModeSetsNeeded(?int $matchModeSetsNeeded): static
    {
        $this->matchModeSetsNeeded = $matchModeSetsNeeded;

        return $this;
    }

    public function getMatchModeLegsNeeded(): ?int
    {
        return $this->matchModeLegsNeeded;
    }

    public function setMatchModeLegsNeeded(?int $matchModeLegsNeeded): static
    {
        $this->matchModeLegsNeeded = $matchModeLegsNeeded;

        return $this;
    }

    public function getToThrowPlayerId(): ?int
    {
        return $this->toThrowPlayerId;
    }

    public function setToThrowPlayerId(?int $toThrowPlayerId): static
    {
        $this->toThrowPlayerId = $toThrowPlayerId;

        return $this;
    }

    /**
    /**
     * @return Collection<int, GameSet>
     */
    public function getSets(): Collection
    {
        return $this->sets;
    }

    public function addSet(GameSet $set): static
    {
        if (!$this->sets->contains($set)) {
            $this->sets->add($set);
            $set->setRelatedGame($this);
        }

        return $this;
    }

    public function removeSet(GameSet $set): static
    {
        if ($this->sets->removeElement($set)) {
            // set the owning side to null (unless already changed)
            if ($set->getGame() === $this) {
                $set->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameLeg>
     */
    public function getLegs(): Collection
    {
        return $this->legs;
    }

    public function addLeg(GameLeg $leg): static
    {
        if (!$this->legs->contains($leg)) {
            $this->legs->add($leg);
            $leg->setGame($this);
        }

        return $this;
    }

    public function removeLeg(GameLeg $leg): static
    {
        if ($this->legs->removeElement($leg)) {
            // set the owning side to null (unless already changed)
            if ($leg->getGame() === $this) {
                $leg->setGame(null);
            }
        }

        return $this;
    }

    #[Groups(['game'])]
    public function getGameMode(): string
    {
        static $mode = null;
        if ($mode === null) {
            $mode = [
                GameTypeX01::class => 'X01',
                GameTypeCricket::class => 'Cricket',
                GameTypeShanghai::class => 'Shanghai',
            ];
        }

        return $mode[static::class] ?? 'unknown';
    }

    /**
     * @return Collection<int, GameTally>
     */
    public function getTally(): Collection
    {
        return $this->tally;
    }

    public function addTally(GameTally $tally): static
    {
        if (!$this->tally->contains($tally)) {
            $this->tally->add($tally);
            $tally->setGame($this);
        }

        return $this;
    }

    public function removeTally(GameTally $tally): static
    {
        if ($this->tally->removeElement($tally)) {
            // set the owning side to null (unless already changed)
            if ($tally->getGame() === $this) {
                $tally->setGame(null);
            }
        }

        return $this;
    }
}
