<?php

namespace App\Controller\Admin;

use App\Entity\Quantity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class QuantityCrudController extends AbstractCrudController
{
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
}
