<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ChangePasswordController extends AbstractController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function index(): Response
    {
        return $this->render('change_password/index.html.twig', [
            'controller_name' => 'ChangePasswordController',
        ]);
    }
}
