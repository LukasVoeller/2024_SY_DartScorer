<?php

namespace App\Entity;

use App\Repository\X01Repository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class GameX01 extends Game
{
    #[ORM\Column(type: "integer", options: ["default" => 0])]
    #[Groups(["api_game"])]
    private ?int $startScore = 0;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(["api_game"])]
    private ?string $finishType = null;

    #[ORM\Column(type: "json", nullable: true)]
    #[Groups(["api_game"])]
    private ?array $player1Averages = null;

    #[ORM\Column(type: "json", nullable: true)]
    #[Groups(["api_game"])]
    private ?array $player2Averages = null;

    public function getStartScore(): ?int
    {
        return $this->startScore;
    }

    public function setStartScore(?int $startScore): static
    {
        $this->startScore = $startScore;

        return $this;
    }

    public function getFinishType(): ?string // Updated getter method name
    {
        return $this->finishType; // Updated property name
    }

    public function setFinishType(?string $finishType): static // Updated setter method name
    {
        $this->finishType = $finishType; // Updated property name

        return $this;
    }

    public function getPlayer1Averages(): ?array
    {
        return $this->player1Averages;
    }

    public function setPlayer1Averages(?array $player1Averages): static
    {
        $this->player1Averages = $player1Averages;

        return $this;
    }

    public function getPlayer2Averages(): ?array
    {
        return $this->player2Averages;
    }

    public function setPlayer2Averages(?array $player2Averages): static
    {
        $this->player2Averages = $player2Averages;

        return $this;
    }
}
