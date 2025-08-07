<?php

namespace App\Controller\Admin;

use App\Entity\Depense;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DepenseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Depense::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('compteSalaire.dateDebutCompte', 'Compte du'),
            DateTimeField::new('compteSalaire.dateFinCompte', 'Au'),
            TextField::new('category.nom', 'Depense'),
            TextField::new('category.prix', 'Prix'),
            BooleanField::new('category.isVital')
        ];
    }
}
