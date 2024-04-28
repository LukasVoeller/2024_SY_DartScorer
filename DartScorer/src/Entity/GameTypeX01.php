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
