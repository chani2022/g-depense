<?php

namespace App\Repository;

use App\Entity\CompteSalaire;
use App\Entity\User;
use DateTime;
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

    public function getCompteSalaireByDate(User $user, DateTime $date): ?CompteSalaire
    {
        return  $this->createQueryBuilder('c')
            ->where('c.dateDebutCompte <= :date AND c.dateFinCompte >= :date')
            ->andWhere('c.owner = :owner')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCompteSalaireWithDateNow(User $user): ?CompteSalaire
    {
        return  $this->createQueryBuilder('c')
            ->where('c.dateDebutCompte <= :date AND c.dateFinCompte >= :date')
            ->andWhere('c.owner = :owner')
            ->setParameter('date', (new DateTime())->format('Y-m-d'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

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
