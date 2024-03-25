<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[ORM\MappedSuperclass]
abstract class Game
{
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    private ?Player $player1 = null;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    private ?Player $player2 = null;

    // TODO: Dont reference as Player Entity?
    #[ORM\ManyToOne(targetEntity: Player::class)]
    private ?Player $playerStarting = null;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    private ?Player $winner = null;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    private ?Player $loser = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $player1Scores = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $player2Scores = null;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $player1Sets = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $player2Sets = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $player1Legs = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $player2Legs = 0;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $matchMode = null;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $matchModeSets = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $matchModeLegs = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $player1Darts = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $player2Darts = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPlayer1(): ?Player
    {
        return $this->player1;
    }

    public function setPlayer1(?Player $player1): static
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?Player
    {
        return $this->player2;
    }

    public function setPlayer2(?Player $player2): static
    {
        $this->player2 = $player2;

        return $this;
    }

    public function getPlayerStarting(): ?Player
    {
        return $this->playerStarting;
    }

    public function setPlayerStarting(?Player $playerStarting): static
    {
        $this->playerStarting = $playerStarting;

        return $this;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function setWinner(?Player $winner): static
    {
        $this->winner = $winner;

        return $this;
    }

    public function getLoser(): ?Player
    {
        return $this->loser;
    }

    public function setLoser(?Player $loser): static
    {
        $this->loser = $loser;

        return $this;
    }

    public function getPlayer1Scores(): ?array
    {
        return $this->player1Scores;
    }

    public function setPlayer1Scores(?array $player1Scores): static
    {
        $this->player1Scores = $player1Scores;

        return $this;
    }

    public function getPlayer2Scores(): ?array
    {
        return $this->player2Scores;
    }

    public function setPlayer2Scores(?array $player2Scores): static
    {
        $this->player2Scores = $player2Scores;

        return $this;
    }

    public function getPlayer1Sets(): ?int
    {
        return $this->player1Sets;
    }

    public function setPlayer1Sets(?int $player1Sets): static
    {
        $this->player1Sets = $player1Sets;

        return $this;
    }

    public function getPlayer2Sets(): ?int
    {
        return $this->player2Sets;
    }

    public function setPlayer2Sets(?int $player2Sets): static
    {
        $this->player2Sets = $player2Sets;

        return $this;
    }

    public function getPlayer1Legs(): ?int
    {
        return $this->player1Legs;
    }

    public function setPlayer1Legs(?int $player1Legs): static
    {
        $this->player1Legs = $player1Legs;

        return $this;
    }

    public function getPlayer2Legs(): ?int
    {
        return $this->player2Legs;
    }

    public function setPlayer2Legs(?int $player2Legs): static
    {
        $this->player2Legs = $player2Legs;

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

    public function setMatchMode(?string $matchMode): static
    {
        $this->matchMode = $matchMode;

        return $this;
    }

    public function getMatchModeSets(): ?int
    {
        return $this->matchModeSets;
    }

    public function setMatchModeSets(?int $matchModeSets): static
    {
        $this->matchModeSets = $matchModeSets;

        return $this;
    }

    public function getMatchModeLegs(): ?int
    {
        return $this->matchModeLegs;
    }

    public function setMatchModeLegs(?int $matchModeLegs): static
    {
        $this->matchModeLegs = $matchModeLegs;

        return $this;
    }

    public function getPlayer1Darts(): ?int
    {
        return $this->player1Darts;
    }

    public function setPlayer1Darts(?int $player1Darts): static
    {
        $this->player1Darts = $player1Darts;

        return $this;
    }

    public function getPlayer2Darts(): ?int
    {
        return $this->player2Darts;
    }

    public function setPlayer2Darts(?int $player2Darts): static
    {
        $this->player2Darts = $player2Darts;

        return $this;
    }
}
