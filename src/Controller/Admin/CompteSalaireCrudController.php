<?php

namespace App\Controller\Admin;

use App\Entity\CompteSalaire;
use App\Validator\DateBeetween;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('ROLE_USER')]
class CompteSalaireCrudController extends AbstractCrudController
{
    public function __construct(private Security $security) {}
    public static function getEntityFqcn(): string
    {
        return CompteSalaire::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', '#')->onlyOnIndex(),
            DateField::new('dateDebutCompte')
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new DateBeetween('strict'),
                ]),
            DateField::new('dateFinCompte')
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new DateBeetween('strict'),
                ]),
            AssociationField::new('owner')->onlyOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDateFormat('medium');
    }
    /**
     * admin peut tous voir
     * et utilisateur, seul leur compte salaire
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $alias = $qb->getAllAliases()[0];

        $user = $this->getUser();
        if (!$this->security->isGranted('ROLE_ADMIN', $user)) {
            $qb->where(sprintf('%s.owner = :user', $alias))
                ->setParameter('user', $this->getUser());
        }

        return $qb;
    }
}
