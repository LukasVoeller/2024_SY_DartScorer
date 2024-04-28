<?php

namespace App\Repository;

use App\Entity\GameLeg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameLeg>
 *
 * @method GameLeg|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameLeg|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameLeg[]    findAll()
 * @method GameLeg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameLeg::class);
    }

    //    /**
    //     * @return Leg[] Returns an array of Leg objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Leg
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
