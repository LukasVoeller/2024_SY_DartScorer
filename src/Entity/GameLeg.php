<?php declare(strict_types=1);

/*
 * The Software is licensed, not sold. You may use the Software only as
 * permitted under the terms of the license agreement. Unauthorized use,
 * modification, distribution, or copying of the Software is strictly prohibited.
 *
 * @copyright  Copyright (c) Lukas Völler.
 * @author     Lukas Völler
 * @license    Proprietary License
 * @link       https://www.vllr.lu
 */

namespace App\Entity;

use App\Repository\LegRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LegRepository::class)]
class GameLeg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_game_full'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'legs')]
    private ?Game $relatedGame = null;

    #[ORM\ManyToOne(inversedBy: 'legs')]
    private ?GameSet $relatedSet = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_game_full'])]
    private ?int $winnerPlayerId = null;

    /**
     * @var Collection<int, GameScore>
     */
    #[ORM\OneToMany(targetEntity: GameScore::class, mappedBy: 'relatedLeg')]
    #[Groups(['api_game_full'])]
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

    public function getRelatedSet(): ?GameSet
    {
        return $this->relatedSet;
    }

    public function setRelatedSet(?GameSet $relatedSet): static
    {
        $this->relatedSet = $relatedSet;

        return $this;
    }

    public function getWinnerPlayerId(): ?int
    {
        return $this->winnerPlayerId;
    }

    public function setWinnerPlayerId(?int $winnerPlayerId): static
    {
        $this->winnerPlayerId = $winnerPlayerId;

        return $this;
    }

    /**
     * @return Collection<int, GameScore>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(GameScore $score): static
    {
        if (!$this->scores->contains($score)) {
            $this->scores->add($score);
            $score->setRelatedLeg($this);
        }

        return $this;
    }

    public function removeScore(GameScore $score): static
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
