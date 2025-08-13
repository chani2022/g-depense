<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Entity\Depense;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DepenseCrudController extends AbstractCrudController
{
    public function __construct(private Security $security) {}
    public static function getEntityFqcn(): string
    {
        return Depense::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('compteSalaire.dateDebutCompte', 'Compte du'),
            DateTimeField::new('compteSalaire.dateFinCompte', 'Au'),

            // AssociationField::new('category')
            //     ->formatValue(function (Category $category) {
            //         return $category->getNom() . ' ' . $category->getPrix();
            //     }),

            TextField::new('category.nom', 'Depense')
                ->formatValue(function (string $nom) {
                    return $nom ?? '';
                }),
            MoneyField::new('category.prix', 'Prix')
                ->setNumDecimals(3)
                ->setCurrency('MGA')
                ->formatValue(function (float $prix) {
                    return $prix ?? 0;
                }),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasDepense = $qb->getAllAliases()[0];
        $qb->select($aliasDepense)
            ->join($aliasDepense . '.compteSalaire', 'cs')
            ->addSelect('cs')
            ->join('cs.owner', 'ow')
            ->addSelect('ow')
            ->join($aliasDepense . '.category', 'cat')
            ->addSelect('cat')
            ->join('cat.quantity', 'q')
            ->addSelect('q');

        if (!$this->security->isGranted('ROLE_ADMIN', $this->getUser())) {
            $qb->where('cs.owner = :user')
                ->setParameter('user', $this->getUser());
        }

        return $qb;
    }
}
