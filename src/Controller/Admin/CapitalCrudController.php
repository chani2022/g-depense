<?php

namespace App\Controller\Admin;

use App\Entity\Capital;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CapitalCrudController extends AbstractCrudController
{

    public function __construct(private Security $security) {}

    public static function getEntityFqcn(): string
    {
        return Capital::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex(),
            AvatarField::new('compteSalaire.owner.imageName')
                ->formatValue(function ($value) {
                    return $value ? '/images/users/' . $value : '/images/users/user-default.png';
                })
                ->setLabel('Proprietaire')
                ->onlyOnIndex(),
            NumberField::new('montant')->setLabel('Montant'),
            NumberField::new('ajout')->setLabel('Ajout')
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasCapital = $qb->getAllAliases()[0];
        $qb->select($aliasCapital . ',cs,ow')
            ->join($aliasCapital . '.compteSalaire', 'cs')
            ->join('cs.owner', 'ow');
        if (!$this->security->isGranted('ROLE_ADMIN', $this->security->getUser())) {
            $qb->where('cs.owner = :owner')
                ->setParameter('owner', $this->security->getUser());
        }

        return $qb;
    }
}
