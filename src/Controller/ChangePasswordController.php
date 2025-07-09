<?php

namespace App\Controller;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ChangePasswordController extends AbstractController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function changePassword(AdminUrlGenerator $adminUrlGenerator): Response
    {
        $url =  $adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(UserCrudController::class)
            ->setAction('changePassword')
            ->generateUrl();

        return $this->redirect($url);
        // return $this->render('change_password/change-password.html.twig', []);
    }
}
