<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["api"])]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    #[Groups(["api"])]
    private ?string $name = null;

    #[ORM\Column(nullable: true, options: ["default" => 0.0])] // Set default value for average
    #[Groups(["api"])]
    private ?float $average = 0.0;

    #[ORM\Column(nullable: true, options: ["default" => 0])] // Set default value for highscore
    #[Groups(["api"])]
    private ?int $highscore = 0;

    #[ORM\Column(type: "integer", nullable: true, options: ["default" => 0])]
    #[Groups(["api"])]
    private ?int $counter180 = 0; // New 180 counter field

    #[ORM\Column(type: "integer", nullable: true, options: ["default" => 0])]
    #[Groups(["api"])]
    private ?int $counterPlayedGames = 0; // New match counter field

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAverage(): ?float
    {
        return $this->average;
    }

    public function setAverage(?float $average): static
    {
        $this->average = $average;

        return $this;
    }

    public function getHighscore(): ?int
    {
        return $this->highscore;
    }

    public function setHighscore(?int $highscore): static
    {
        $this->highscore = $highscore;

        return $this;
    }

    public function getCounter180(): ?int
    {
        return $this->counter180;
    }

    public function setCounter180(?int $counter180): static
    {
        $this->counter180 = $counter180;

        return $this;
    }

    public function getCounterPlayedGames(): ?int
    {
        return $this->counterPlayedGames;
    }

    public function setCounterPlayedGames(?int $counterPlayedGames): static
    {
        $this->counterPlayedGames = $counterPlayedGames;

        return $this;
    }
}
