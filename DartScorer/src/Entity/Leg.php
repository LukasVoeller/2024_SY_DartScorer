<?php

namespace App\Entity;

use App\Repository\LegRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LegRepository::class)]
class Leg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'legs')]
    private ?Game $relatedGame = null;

    #[ORM\ManyToOne(inversedBy: 'legs')]
    private ?Set $relatedSet = null;

    #[ORM\Column(nullable: true)]
    private ?int $playerIdWinner = null;

    /**
     * @var Collection<int, Score>
     */
    #[ORM\OneToMany(targetEntity: Score::class, mappedBy: 'relatedLeg')]
    private Collection $scores;

    public function __construct()
    {
        $this->scores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRelatedSet(): ?Set
    {
        return $this->relatedSet;
    }

    public function setRelatedSet(?Set $relatedSet): static
    {
        $this->relatedSet = $relatedSet;

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
            $score->setRelatedLeg($this);
        }

        return $this;
    }

    public function removeScore(Score $score): static
    {
        if ($this->scores->removeElement($score)) {
            // set the owning side to null (unless already changed)
            if ($score->getRelatedLeg() === $this) {
                $score->setRelatedLeg(null);
            }
        }

        return $this;
    }
}
