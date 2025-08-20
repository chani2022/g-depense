<?php

namespace App\Repository;

use App\Entity\Depense;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 *
 * @method Depense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depense[]    findAll()
 * @method Depense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    //    /**
    //     * @return Depense[] Returns an array of Depense objects
    //     */
    public function getDepenseBetweenDateWithCapital(?User $user = null, ?array $dates = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->select("
            CONCAT(DATE_FORMAT(cs.dateDebutCompte, '%d/%m/%Y),  ' - ' , DATE_FORMAT(cs.dateFinCompte, '%d/%m/%Y)) AS label,
            SUM(d.prix) AS total_depense, 
            (COALESCE(cap.montant,0) + COALESCE(cap.ajout, 0)) AS total_capital")
            ->join('d.compteSalaire', 'cs')
            ->join('cs.capitals', 'cap')
            ->join('cs.owner', 'ow');
        /**
         * utilisateur simple
         */
        if ($user) {
            $qb->andWhere('cs.owner = :user')
                ->setParameter('user', $user);
        }
        /**
         * date aujourd'hui, par defaut
         */
        if (!$dates) {
            $qb->andWhere('cs.dateDebutCompte <= :date AND cs.dateFinCompte >= :date')
                ->setParameter('date', new DateTime());
        }
        return $qb
            ->groupBy('ow.id')
            ->addGroupBy('cs.dateDebutCompte')
            ->orderBy('cs.dateDebutCompte', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Depense
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
