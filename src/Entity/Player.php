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

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_player_plain', 'api_player_full', 'api_user'])]
    private ?int $id = null;

    #[ORM\Column(length: 32, nullable: false)]
    #[Groups(['api_player_plain', 'api_player_full', 'api_user'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api_player_full', 'api_user'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'player')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    //#[Groups(['api_player'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'player1', targetEntity: Game::class)]
    private Collection $gamesAsPlayer1;

    #[ORM\OneToMany(mappedBy: 'player2', targetEntity: Game::class)]
    private Collection $gamesAsPlayer2;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->gamesAsPlayer1 = new ArrayCollection();
        $this->gamesAsPlayer2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesAsPlayer1(): Collection
    {
        return $this->gamesAsPlayer1;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesAsPlayer2(): Collection
    {
        return $this->gamesAsPlayer2;
    }

    // TODO: Outsource
    public function getTotalGamesPlayed(): int
    {
        return count($this->gamesAsPlayer1) + count($this->gamesAsPlayer2);
    }

    public function getTotalGamesWon(): int
    {
        $gamesWonAsPlayer1 = $this->gamesAsPlayer1->filter(function (Game $game) {
            return $game->getState() === 'Finished' && $game->getWinnerPlayerId() === $this->id;
        })->count();

        $gamesWonAsPlayer2 = $this->gamesAsPlayer2->filter(function (Game $game) {
            return $game->getState() === 'Finished' && $game->getWinnerPlayerId() === $this->id;
        })->count();

        return $gamesWonAsPlayer1 + $gamesWonAsPlayer2;
    }

    public function getTotalGamesLost(): int
    {
        $lostGamesAsPlayer1 = $this->gamesAsPlayer1->filter(function (Game $game) {
            return $game->getState() === 'Finished' && $game->getWinnerPlayerId() !== $this->id;
        })->count();

        $lostGamesAsPlayer2 = $this->gamesAsPlayer2->filter(function (Game $game) {
            return $game->getState() === 'Finished' && $game->getWinnerPlayerId() !== $this->id;
        })->count();

        return $lostGamesAsPlayer1 + $lostGamesAsPlayer2;
    }

    public function getLiveGamesCount(): int
    {
        $liveGamesAsPlayer1 = $this->gamesAsPlayer1->filter(function (Game $game) {
            return $game->getState() === 'Live';
        })->count();

        $liveGamesAsPlayer2 = $this->gamesAsPlayer2->filter(function (Game $game) {
            return $game->getState() === 'Live';
        })->count();

        return $liveGamesAsPlayer1 + $liveGamesAsPlayer2;
    }

    public function getFinishedGamesCount(): int
    {
        $finishedGamesAsPlayer1 = $this->gamesAsPlayer1->filter(function (Game $game) {
            return $game->getState() === 'Finished';
        })->count();

        $finishedGamesAsPlayer2 = $this->gamesAsPlayer2->filter(function (Game $game) {
            return $game->getState() === 'Finished';
        })->count();

        return $finishedGamesAsPlayer1 + $finishedGamesAsPlayer2;
    }
}
