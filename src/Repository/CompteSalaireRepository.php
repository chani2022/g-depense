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

    public function getCompteSalaireByDate(string $date): ?CompteSalaire
    {
        // $field = 'dateFinCompte';
        // if ($isDateDebut) {
        //     $field = 'dateDebutCompte';
        // }
        return  $this->createQueryBuilder('c')
            ->where('c.dateDebutCompte <= :date AND c.dateFinCompte >= :date')
            ->setParameter('date', $date)
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
