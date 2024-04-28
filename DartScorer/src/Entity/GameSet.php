<?php

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SetRepository::class)]
class GameSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['set'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $relatedGame = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['set'])]
    private ?int $playerIdWinner = null;

    #[ORM\Column]
    #[Groups(['set'])]
    private ?int $matchModeLegsNeeded = null;

    /**
     * @var Collection<int, GameLeg>
     */
    #[ORM\OneToMany(targetEntity: GameLeg::class, mappedBy: 'relatedSet')]
    #[Groups(['set'])]
    private Collection $legs;

    public function __construct()
    {
        $this->legs = new ArrayCollection();
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

    public function getPlayerIdWinner(): ?int
    {
        return $this->playerIdWinner;
    }

    public function setPlayerIdWinner(?int $playerIdWinner): static
    {
        $this->playerIdWinner = $playerIdWinner;

        return $this;
    }

    public function getMatchModeLegsNeeded(): ?int
    {
        return $this->matchModeLegsNeeded;
    }

    public function setMatchModeLegsNeeded(int $matchModeLegsNeeded): static
    {
        $this->matchModeLegsNeeded = $matchModeLegsNeeded;

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
            $leg->setRelatedSet($this);
        }

        return $this;
    }

    public function removeLeg(GameLeg $leg): static
    {
        if ($this->Legs->removeElement($leg)) {
            // set the owning side to null (unless already changed)
            if ($leg->getRelatedSet() === $this) {
                $leg->setRelatedSet(null);
            }
        }

        return $this;
    }
}
