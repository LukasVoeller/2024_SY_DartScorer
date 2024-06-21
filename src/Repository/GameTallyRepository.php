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

namespace App\Repository;

use App\Entity\GameTally;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTally>
 */
class GameTallyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTally::class);
    }

    public function findByGameIdAndPlayerId(int $gameId, int $playerId): ?GameTally
    {
        // Creating a query builder instance with alias 'g' for GameTally
        return $this->createQueryBuilder('g')
            ->innerJoin('g.game', 'game')  // Assuming 'game' is the property in GameLTally that refers to the Game entity
            ->andWhere('game.id = :gameId')  // Using the ID of the game entity
            ->setParameter('gameId', $gameId)
            ->andWhere('g.playerId = :playerId')  // Assuming 'playerId' is directly accessible and not a relation (if it's a relation, similar treatment as 'game' is needed)
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getOneOrNullResult();  // Get a single result or null
    }
}
