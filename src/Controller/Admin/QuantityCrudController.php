<?php

namespace App\Controller\Admin;

use App\Entity\Quantity;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            NumberField::new('quantite', 'Quantite')
                ->setFormTypeOptions([
                    'constraints' => [
                        new NotBlank()
                    ]
                ]),
            TextField::new('unite', 'Unité')->setFormTypeOptions([
                'constraints' => [
                    new NotBlank()
                ]
            ]),
            AvatarField::new('owner', 'Propriétaire')
                ->formatValue(function (User $value) {
                    return $value->getImageName() ? '/images/users/' . $value->getImageName() : '/images/users/user-default.png';
                })
                ->onlyOnIndex()
                ->setPermission('ROLE_ADMIN')
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
