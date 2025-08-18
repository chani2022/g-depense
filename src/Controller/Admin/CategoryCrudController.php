<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('ROLE_USER')]
class CategoryCrudController extends AbstractCrudController
{
    public function __construct(private Security $security) {}
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', '#')->onlyOnIndex(),
            TextField::new('nom', 'Nom du categorie')->setFormTypeOption('constraints', [
                new NotBlank()
            ]),
            AvatarField::new('owner.imageName', 'Avatar')
                ->formatValue(function (?string $imageName) {
                    return $imageName ? '/images/users/' . $imageName : '/images/users/user-default.png';
                })->setPermission('ROLE_ADMIN')
                ->onlyOnIndex()
                ->onlyOnDetail(),

            AssociationField::new('owner', 'Proprietaire')
                ->onlyOnForms()
                ->setPermission('ROLE_ADMIN')
                ->setFormTypeOptions([
                    'placeholder' => '-Selectionnez-',
                    'constraints' => [
                        new NotBlank()
                    ]
                ])
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasCategory = $qb->getAllAliases()[0];
        $qb->join($aliasCategory . '.owner', 'ow')
            ->addSelect('ow');

        if (!$this->security->isGranted('ROLE_ADMIN', $this->security->getUser())) {
            $qb->where('ow = :owner')
                ->setParameter('owner', $this->security->getUser());
        }
        return $qb;
    }
}
