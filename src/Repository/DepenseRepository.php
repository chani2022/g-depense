<?php

namespace App\Repository;

use App\Entity\CompteSalaire;
use App\Entity\Depense;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    private array $parameters = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    //    /**
    //     * @return Depense[] Returns an array of Depense objects
    //     */
    public function getDepenseBetweenDateWithCapital(User $user, ?array $dates = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->select("
            ow.id,
            CONCAT(DATE_FORMAT(cs.dateDebutCompte, '%d/%m/%Y'),  ' - ' , DATE_FORMAT(cs.dateFinCompte, '%d/%m/%Y')) AS label,
            SUM(d.prix) AS total_depense, 
            (COALESCE(cap.montant,0) + COALESCE(cap.ajout, 0)) AS total_capital")
            ->join('d.compteSalaire', 'cs')
            ->join('cs.capitals', 'cap')
            ->join('cs.owner', 'ow')
            ->andWhere('ow = :user');

        $this->parameters['user'] = $user;

        $qb = $this->applyWhereClause($qb, $dates);

        foreach ($this->parameters as $bind => $value) {
            $qb->setParameter($bind, $value);
        }

        return $qb->groupBy('ow.id')
            ->addGroupBy('cs.dateDebutCompte')
            ->orderBy('cs.dateDebutCompte', 'ASC')
            ->getQuery()
            ->getResult();
    }

    private function applyWhereClause(QueryBuilder $qb, ?array $dates = null): QueryBuilder
    {
        /**
         * prendre les depense d'aujourd'hui
         * si la date n'est pas renseigné
         */
        if (!$dates) {
            $qb->andWhere(':date BETWEEN cs.dateDebutCompte AND cs.dateFinCompte');
            $this->parameters['date'] = new DateTime();
        } else {
            $idsCompteSalaireBetweenDate = $this->_em->createQueryBuilder()
                ->select('cs1.id')
                ->from(CompteSalaire::class, 'cs1')
                ->join('cs1.owner', 'ow1')
                ->where('cs1.dateDebutCompte BETWEEN :debut AND :fin')
                ->andWhere('ow1 = :user')
                ->getDQL();

            $qb->andWhere('cs.id IN (' . $idsCompteSalaireBetweenDate . ')');
            $this->parameters['debut'] = $dates[0];
            $this->parameters['fin'] = $dates[1];
        }
        return $qb;
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
