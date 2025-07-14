<?php

namespace App\Repository;

use App\Entity\CompteSalaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteSalaire>
 *
 * @method CompteSalaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompteSalaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompteSalaire[]    findAll()
 * @method CompteSalaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteSalaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteSalaire::class);
    }

//    /**
//     * @return CompteSalaire[] Returns an array of CompteSalaire objects
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

//    public function findOneBySomeField($value): ?CompteSalaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
