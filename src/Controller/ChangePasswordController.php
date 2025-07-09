<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ChangePasswordController extends AbstractDashboardController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function changePassword(): Response
    {
        return $this->render('change_password/change-password.html.twig', []);
    }
}
