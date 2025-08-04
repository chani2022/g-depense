<?php

namespace App\Repository;

use App\Entity\Quantity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quantity>
 *
 * @method Quantity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quantity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quantity[]    findAll()
 * @method Quantity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuantityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quantity::class);
    }

    //    /**
    //     * @return Quantity[] Returns an array of Quantity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    // public function getQueryBuilderByUser(User $user): QueryBuilder
    // {
    //     return $this->createQueryBuilder('q')
    //         ->join('q.owner', 'ow')
    //         ->addSelect('ow')
    //         ->where('ow = :owner')
    //         ->setParameter('owner', $user);
    // }
}
