<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class GameTypeX01 extends Game
{
    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(['game'])]
    private ?int $startScore;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(['game'])]
    private ?int $player1Score;

    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(['game'])]
    private ?int $player2Score;

    #[ORM\Column(type: "string", nullable: false)]
    #[Groups(['game'])]
    private ?string $finishType;

    public function getStartScore(): ?int
    {
        return $this->startScore;
    }

    public function setStartScore(?int $startScore): static
    {
        $this->startScore = $startScore;

        return $this;
    }

    public function getPlayer1Score(): ?int
    {
        return $this->player1Score;
    }

    public function setPlayer1Score(?int $player1Score): static
    {
        $this->player1Score = $player1Score;

        return $this;
    }

    public function getPlayer2Score(): ?int
    {
        return $this->player2Score;
    }

    public function setPlayer2Score(?int $player2Score): static
    {
        $this->player2Score = $player2Score;

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
}
