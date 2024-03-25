<?php

namespace App\Repository;

use App\Entity\GameX01;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameX01>
 *
 * @method GameX01|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameX01|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameX01[]    findAll()
 * @method GameX01[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class X01Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameX01::class);
    }

    //    /**
    //     * @return GameX01[] Returns an array of GameX01 objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('x')
    //            ->andWhere('x.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('x.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GameX01
    //    {
    //        return $this->createQueryBuilder('x')
    //            ->andWhere('x.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
