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

    /**
     * Permet de recuperer le total de depense et capital par utilisateur
     * et par compte salaire.
     * 
     * @param User $user                 utilisateur authentifié
     * @param string[]|null $dates       date de recherche
     * @return array<int, array{string, string|int}>
     */
    public function findDepensesWithCapital(User $user, ?array $dates = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->select("
            ow.id,
            CONCAT(DATE_FORMAT(cs.dateDebutCompte, '%d/%m/%Y'),  ' - ' , DATE_FORMAT(cs.dateFinCompte, '%d/%m/%Y')) AS label,
            SUM(d.prix) AS total_depense, 
            SUM(COALESCE(cap.montant,0) + COALESCE(cap.ajout, 0)) AS total_capital")
            ->join('d.compteSalaire', 'cs')
            ->leftJoin('cs.capitals', 'cap')
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
    /**
     * Permet de recuperer le total de depense et capital
     * d'une date donnée sinon date d'aujourd'hui (defaut)
     * 
     * @param User $user                        utilisateur authentifié
     * @param string[]|null $dates              date de recherche
     * @return array<int, array{string, float|null}>
     */
    public function getTotalDepenseAndCapitalInDateGivingByUser(User $user, ?array $dates = null): array
    {
        $qb =  $this->createQueryBuilder('d')
            ->select('
                SUM(d.prix) AS total_depense_general,
                SUM(COALESCE(cap.montant,0) + COALESCE(cap.ajout, 0)) AS total_capital_general
            ')
            ->leftJoin('d.compteSalaire', 'cs')
            ->leftJoin('cs.owner', 'ow')
            ->leftJoin('cs.capitals', 'cap')
            ->andWhere('ow = :user')
            ->setParameter('user', $user);

        if ($dates) {
            $qb->andWhere('(cs.dateDebutCompte BETWEEN :debut AND :fin) OR (cs.dateFinCompte BETWEEN :debut AND :fin)')
                ->setParameter('debut', $dates[0])
                ->setParameter('fin', $dates[1]);
        } else {
            $qb->andWhere(':date BETWEEN cs.dateDebutCompte AND cs.dateFinCompte')
                ->setParameter('date', new DateTime());
        };
        return $qb
            ->getQuery()
            ->getScalarResult();
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
            $dqlIdsCompteSalaireBetweenDate = $this->createSubQuery(
                CompteSalaire::class,
                'cs1',
                '((cs1.dateDebutCompte BETWEEN :debut AND :fin)
                 OR (cs1.dateFinCompte BETWEEN :debut AND :fin))
                 AND (ow1 = :user)',
                'cs1.id',
                [
                    [
                        'join' => 'cs1.owner',
                        'alias' => 'ow1'
                    ]
                ]
            );

            $qb->andWhere('cs.id IN (' . $dqlIdsCompteSalaireBetweenDate . ')');
            $this->parameters['debut'] = $dates[0];
            $this->parameters['fin'] = $dates[1];
        }
        return $qb;
    }

    private function createSubQuery(string $from, string $alias, string $where, ?string $select = null, ?array $joins = null): string
    {
        $qb = $this->_em->createQueryBuilder()
            ->select($select)
            ->from($from, $alias);
        if ($joins) {
            foreach ($joins as $join) {
                $qb->join($join['join'], $join['alias']);
            }
        }

        return $qb->andWhere($where)
            ->getDQL();
    }
}
