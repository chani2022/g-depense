<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Quantity;
use App\Validator\UniqueCategory;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

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
            TextField::new('nom', 'Nom')->setFormTypeOption('constraints', [
                new NotBlank(),
                new UniqueCategory()
            ]),
            NumberField::new('prix', 'Prix')->setFormTypeOptions([
                'constraints' => [
                    new NotBlank(),
                    new Positive()
                ]
            ]),
            BooleanField::new('isVital', 'Primordial')->onlyOnForms(),
            AssociationField::new('quantity', 'Quantity')
                ->formatValue(function (?Quantity $quantity = null) {
                    return $quantity ? $quantity->getUnite() : '';
                })
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $aliasCategory = $qb->getAllAliases()[0];

        if (!$this->security->isGranted('ROLE_ADMIN', $this->security->getUser())) {
            $qb->join($aliasCategory . '.owner', 'ow')
                ->where('ow = :owner')
                ->setParameter('owner', $this->security->getUser());
        }
        return $qb;
    }
}
