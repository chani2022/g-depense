<?php

namespace App\Controller\Admin;

use App\Entity\Quantity;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class QuantityCrudController extends AbstractCrudController
{
    public function __construct(private Security $security) {}
    public static function getEntityFqcn(): string
    {
        return Quantity::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('unite', 'Unité'),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasQuantity = $qb->getAllAliases()[0];
        $qb->select($aliasQuantity . ',ow')
            ->join($aliasQuantity . '.owner', 'ow');

        if (!$this->security->isGranted('ROLE_ADMIN', $this->security->getUser())) {
            $qb->where(sprintf('%s.owner = :user', $aliasQuantity))
                ->setParameter('user', $this->getUser());
        }

        return $qb;
    }
}
