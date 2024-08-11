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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class GameTypeX01 extends Game
{
    #[ORM\Column(type: "integer", nullable: false)]
    #[Groups(['api_player_plain', 'api_game_full'])]
    private ?int $startScore;

    #[ORM\Column(type: "string", nullable: false)]
    #[Groups(['api_player_plain', 'api_game_full'])]
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
