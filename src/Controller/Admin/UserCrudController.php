<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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

    public function configureActions(Actions $actions): Actions
    {
        $actionName = 'changePassword';
        $changePassword = Action::new($actionName, 'changement de mot de passe', 'fa fa-file-invoice')
            ->linkToCrudAction('changePassword');

        return $actions
            ->add(Crud::PAGE_INDEX, $changePassword)->setPermission($changePassword, 'ROLE_USER');
    }

    public function changePassword(AdminContext $context): Response
    {
        return $this->render('change_password/change-password.html.twig');
    }
}
