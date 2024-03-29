<?php

namespace App\Repository;

use App\Entity\GameCricket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameCricket>
 *
 * @method GameCricket|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameCricket|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameCricket[]    findAll()
 * @method GameCricket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CricketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameCricket::class);
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
