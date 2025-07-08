<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            TextField::new('username', 'Nom d\'utilisateur'),
        ];
    }

    // public function configureActions(Actions $actions): Actions
    // {
    //     $profilAction = Action::new('profil', 'Profil', 'fa fa-envelope')
    //         ->linkToRoute('app_profil');

    //     return $actions
    //         ->add(Crud::PAGE_EDIT, $profilAction)
    //         ->setPermission(Action::EDIT, 'ROLE_USER');
    // }

    public function profile(AdminContext $adminContext): Response
    {
        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
}
