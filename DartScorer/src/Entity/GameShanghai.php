<?php

namespace App\Entity;

use App\Repository\ShanghaiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShanghaiRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "game_type", type: "string")]
#[ORM\DiscriminatorMap([
    "shanghai" => "GameShanghai"
])]
class GameShanghai extends Game
{
    // TODO: Manage Endscores in an array per Leg played
    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $endScorePlayer1 = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private ?int $endScorePlayer2 = 0;

    public function getEndScorePlayer1(): ?int
    {
        return $this->endScorePlayer1;
    }

    public function setEndScorePlayer1(?int $endScorePlayer1): static
    {
        $this->endScorePlayer1 = $endScorePlayer1;

        return $this;
    }

    public function getEndScorePlayer2(): ?int
    {
        return $this->endScorePlayer2;
    }

    public function setEndScorePlayer2(?int $endScorePlayer2): static
    {
        $this->endScorePlayer2 = $endScorePlayer2;

        return $this;
    }
}
