<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['score'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['score'])]
    private ?int $playerId;

    #[ORM\ManyToOne(inversedBy: 'scores')]
    private ?Game $relatedGame = null;

    #[ORM\ManyToOne(inversedBy: 'scores')]
    private ?Leg $relatedLeg = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['score'])]
    private ?int $value;

    #[ORM\Column(nullable: false)]
    #[Groups(['score'])]
    private ?int $dartsThrown;

    #[ORM\Column(nullable: true)]
    private ?int $dart1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $dart2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $dart3 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    public function setPlayerId(?int $playerId): static
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getRelatedGame(): ?Game
    {
        return $this->relatedGame;
    }

    public function setRelatedGame(?Game $relatedGame): static
    {
        $this->relatedGame = $relatedGame;

        return $this;
    }

    public function getRelatedLeg(): ?Leg
    {
        return $this->relatedLeg;
    }

    public function setRelatedLeg(?Leg $relatedLeg): static
    {
        $this->relatedLeg = $relatedLeg;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDartsThrown(): ?int
    {
        return $this->dartsThrown;
    }

    public function setDartsThrown(int $darts): static
    {
        $this->dartsThrown = $darts;

        return $this;
    }

    public function getDart1(): ?int
    {
        return $this->dart1;
    }

    public function setDart1(?int $dart1): static
    {
        $this->dart1 = $dart1;

        return $this;
    }

    public function getDart2(): ?int
    {
        return $this->dart2;
    }

    public function setDart2(?int $dart2): static
    {
        $this->dart2 = $dart2;

        return $this;
    }

    public function getDart3(): ?int
    {
        return $this->dart3;
    }

    public function setDart3(?int $dart3): static
    {
        $this->dart3 = $dart3;

        return $this;
    }
}
