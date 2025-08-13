<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Depense;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
            // index et detail
            DateTimeField::new('compteSalaire.dateDebutCompte', 'Compte du')
                ->onlyOnIndex()
                ->onlyOnDetail(),
            DateTimeField::new('compteSalaire.dateFinCompte', 'Au')
                ->onlyOnIndex()
                ->onlyOnDetail(),
            TextField::new('category.nom', 'Depense')
                ->onlyOnIndex()
                ->onlyOnDetail(),
            MoneyField::new('category.prix', 'Prix')
                ->onlyOnIndex()
                ->onlyOnDetail()
                ->setNumDecimals(3)
                ->setCurrency('MGA'),
            NumberField::new('category.quantity.quantity', 'Quantite')
                ->onlyOnIndex()
                ->onlyOnDetail(),
            //form
            AssociationField::new('category', 'Category')
                ->onlyOnForms(),
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
