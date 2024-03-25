<?php

namespace App\Repository;

use App\Entity\GameShanghai;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameShanghai>
 *
 * @method GameShanghai|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameShanghai|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameShanghai[]    findAll()
 * @method GameShanghai[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShanghaiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameShanghai::class);
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
