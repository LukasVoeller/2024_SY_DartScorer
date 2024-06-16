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

use App\Entity\GameTypeCricket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTypeCricket>
 *
 * @method GameTypeCricket|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTypeCricket|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTypeCricket[]    findAll()
 * @method GameTypeCricket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CricketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTypeCricket::class);
    }

    //    /**
    //     * @return GameCricket[] Returns an array of GameCricket objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GameCricket
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
