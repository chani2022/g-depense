<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly string $pathUploadedFile) {}

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Depense Mensuel');
    }
    /**
     * @param User $user
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {

        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'app_profil')->setPermission('ROLE_USER'),
                MenuItem::linkToRoute('Change password', 'fa fa-id-card', 'app_change_password')->setPermission('ROLE_USER')
            ]);
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::linkToCrud('User', 'fa fa-users', User::class)->setPermission('ROLE_ADMIN')
        ];
    }
}
