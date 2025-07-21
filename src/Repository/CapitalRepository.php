<?php

namespace App\Repository;

use App\Entity\Capital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Capital>
 *
 * @method Capital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Capital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Capital[]    findAll()
 * @method Capital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CapitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Capital::class);
    }

//    /**
//     * @return Capital[] Returns an array of Capital objects
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

//    public function findOneBySomeField($value): ?Capital
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
