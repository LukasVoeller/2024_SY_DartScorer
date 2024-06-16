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

use App\Entity\GameTypeX01;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTypeX01>
 *
 * @method GameTypeX01|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTypeX01|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTypeX01[]    findAll()
 * @method GameTypeX01[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class X01Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTypeX01::class);
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
