<?php

namespace App\Repository;

use App\Entity\GameTypeShanghai;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTypeShanghai>
 *
 * @method GameTypeShanghai|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTypeShanghai|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTypeShanghai[]    findAll()
 * @method GameTypeShanghai[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShanghaiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTypeShanghai::class);
    }

    //    /**
    //     * @return GameShanghai[] Returns an array of GameShanghai objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GameShanghai
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
