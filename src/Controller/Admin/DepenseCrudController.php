<?php

namespace App\Controller\Admin;

use App\Entity\Depense;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;

#[IsGranted('ROLE_USER')]
class DepenseCrudController extends AbstractCrudController
{
    /**
     * @param Security $security
     */
    public function __construct(private Security $security) {}

    /**
     * @return FieldInterface[]
     */
    public static function getFieldsDefault(): array
    {
        return [
            TextField::new('nomDepense', 'Nom de la Depense'),
            MoneyField::new('prix', 'Prix')
                ->setNumDecimals(3)
                ->setCurrency('MGA'),
            BooleanField::new('vital', 'Obligatoire'),
        ];
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Depense::class;
    }
    /**
     * @param string $pagename
     * @return FieldInterface[]|string[]
     */
    public function configureFields(string $pageName): iterable
    {
        return match ($pageName) {
            Crud::PAGE_INDEX => $this->fieldsIndexPageName(),
            Crud::PAGE_NEW => $this->fieldsNewPageName(),
            default => self::getFieldsDefault(),
        };
    }
    /**
     * Construit une requête personnalisée pour l'index.
     * - Filtre les dépenses par utilisateur si non-admin.
     * - Joint les relations nécessaires (compte salaire, catégorie, quantité).
     * 
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasDepense = $qb->getAllAliases()[0];
        $qb->join($aliasDepense . '.compteSalaire', 'cs')
            ->addSelect('cs')
            ->join('cs.owner', 'ow')
            ->addSelect('ow')
            ->join($aliasDepense . '.category', 'cat')
            ->addSelect('cat')
            ->join($aliasDepense . '.quantity', 'q')
            ->addSelect('q');

        if (!$this->security->isGranted('ROLE_ADMIN', $this->getUser())) {
            $qb->andWhere('cs.owner = :user')
                ->setParameter('user', $this->getUser());
        }

        return $qb;
    }

    /**
     * @return FieldInterface[]|string[]
     */
    protected function fieldsIndexPageName(): array
    {
        $fields = [
            AvatarField::new('compteSalaire.owner.imageName', 'Avatar')
                ->formatValue(function (?string $imageName) {
                    return $imageName ? '/images/users/' . $imageName : '/images/users/user-default.png';
                })->setPermission('ROLE_ADMIN'),
            DateTimeField::new('compteSalaire.dateDebutCompte', 'Compte du')
                ->onlyOnIndex(),
            DateTimeField::new('compteSalaire.dateFinCompte', 'au')
                ->onlyOnIndex(),
            TextField::new('quantity.unite', 'Unité')
                ->onlyOnIndex(),
            NumberField::new('quantity.quantite', 'Quantite')
                ->onlyOnIndex(),
        ];
        return array_merge($fields, self::getFieldsDefault());
    }

    /**
     * @return FieldInterface[]|string[]
     */
    protected function fieldsNewPageName(): array
    {
        $fields = [
            AssociationField::new('category', 'Category')
                ->onlyOnForms(),
            AssociationField::new('quantity', 'Quantité')
                ->onlyOnForms(),
        ];
        return array_merge($fields, self::getFieldsDefault());
    }
}
