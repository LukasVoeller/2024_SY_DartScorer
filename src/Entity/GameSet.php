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
    #[Groups(['api_game_full'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $relatedGame = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_game_full'])]
    private ?int $winnerPlayerId = null;

    #[ORM\Column]
    #[Groups(['set'])]
    private ?int $matchModeLegsNeeded = null;

    /**
     * @var Collection<int, GameLeg>
     */
    #[ORM\OneToMany(targetEntity: GameLeg::class, mappedBy: 'relatedSet')]
    #[Groups(['api_game_full'])]
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

    public function getWinnerPlayerId(): ?int
    {
        return $this->winnerPlayerId;
    }

    public function setWinnerPlayerId(?int $winnerPlayerId): static
    {
        $this->winnerPlayerId = $winnerPlayerId;

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
