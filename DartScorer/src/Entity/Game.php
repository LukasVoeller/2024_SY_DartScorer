<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

//#[ORM\MappedSuperclass]
#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "game_type", type: "string")]
#[ORM\DiscriminatorMap([
    "x01" => "GameX01",
    "cricket" => "GameCricket",
    "shanghai" => "GameShanghai",
])]
abstract class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['game'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $player1Id;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?string $player1Name;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $player2Id;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?string $player2Name;

    #[ORM\Column(nullable: false)]
    #[Groups(['game'])]
    private ?int $playerIdStarting;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $playerIdWinner = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['game'])]
    private ?string $state = null;

    #[ORM\Column(length: 32, nullable: false)]
    #[Groups(['game'])]
    private ?string $matchMode;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $matchModeSetsNeeded = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game'])]
    private ?int $matchModeLegsNeeded = null;

    /**
     * @var Collection<int, Set>
     */
    #[ORM\OneToMany(targetEntity: Set::class, mappedBy: 'relatedGame', orphanRemoval: true)]
    #[Groups(['game'])]
    private Collection $sets;

    /**
     * @var Collection<int, Leg>
     */
    #[ORM\OneToMany(targetEntity: Leg::class, mappedBy: 'relatedGame', orphanRemoval: true)]
    #[Groups(['game'])]
    private Collection $legs;

    /**
     * @var Collection<int, Score>
     */
    #[ORM\OneToMany(targetEntity: Score::class, mappedBy: 'relatedGame', orphanRemoval: true)]
    #[Groups(['game'])]
    private Collection $scores;

    public function __construct()
    {
        $this->sets = new ArrayCollection();
        $this->legs = new ArrayCollection();
        $this->scores = new ArrayCollection();
        $this->date = new \DateTime();
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

    public function getPlayer1Name(): ?string
    {
        return $this->player1Name;
    }

    public function setPlayer1Name(?string $player1Name): static
    {
        $this->player1Name = $player1Name;

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

    public function getPlayer2Name(): ?string
    {
        return $this->player2Name;
    }

    public function setPlayer2Name(?string $player2Name): static
    {
        $this->player2Name = $player2Name;

        return $this;
    }

    public function getPlayerIdStarting(): ?int
    {
        return $this->playerIdStarting;
    }

    public function setPlayerIdStarting(int $playerIdStarting): static
    {
        $this->playerIdStarting = $playerIdStarting;

        return $this;
    }

    public function getPlayerIdWinner(): ?int
    {
        return $this->playerIdWinner;
    }

    public function setPlayerIdWinner(?int $playerIdWinner): static
    {
        $this->playerIdWinner = $playerIdWinner;

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

    /**
     * @return Collection<int, Set>
     */
    public function getSets(): Collection
    {
        return $this->sets;
    }

    public function addSet(Set $set): static
    {
        if (!$this->sets->contains($set)) {
            $this->sets->add($set);
            $set->setRelatedGame($this);
        }

        return $this;
    }

    public function removeSet(Set $set): static
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
     * @return Collection<int, Leg>
     */
    public function getLegs(): Collection
    {
        return $this->legs;
    }

    public function addLeg(Leg $leg): static
    {
        if (!$this->legs->contains($leg)) {
            $this->legs->add($leg);
            $leg->setGame($this);
        }

        return $this;
    }

    public function removeLeg(Leg $leg): static
    {
        if ($this->legs->removeElement($leg)) {
            // set the owning side to null (unless already changed)
            if ($leg->getGame() === $this) {
                $leg->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Score>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Score $score): static
    {
        if (!$this->scores->contains($score)) {
            $this->scores->add($score);
            $score->setGame($this);
        }

        return $this;
    }

    public function removeScore(Score $score): static
    {
        if ($this->scores->removeElement($score)) {
            // set the owning side to null (unless already changed)
            if ($score->getGame() === $this) {
                $score->setGame(null);
            }
        }

        return $this;
    }
}
