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

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class GameScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['score'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_score_plain'])]
    private ?int $playerId;

    #[ORM\ManyToOne(inversedBy: 'scores')]
    private ?GameLeg $relatedLeg = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_score_plain', 'api_game_full'])]
    private ?int $value;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_score_plain'])]
    private ?int $dartsThrown;

    #[ORM\Column(nullable: false)]
    #[Groups(['api_score_plain'])]
    private ?bool $checkout = false;

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

    public function getRelatedLeg(): ?GameLeg
    {
        return $this->relatedLeg;
    }

    public function setRelatedLeg(?GameLeg $relatedLeg): static
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

    public function getCheckout(): ?bool
    {
        return $this->checkout;
    }

    public function setCheckout(bool $checkout): static
    {
        $this->checkout = $checkout;

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
