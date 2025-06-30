<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot/password', name: 'app_security_forgot_password')]
    public function generateNewPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();
            /** @var User */
            $user = $em->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);

            if (!$user) {
                $this->addFlash('danger', 'L\'utilisateur ' . $username . ' introuvable');
                return $this->redirectToRoute("app_security_forgot_password");
            }

            $new_password = 'test';
            $password_hash = $hasher->hashPassword($user, $new_password);

            $user->setPassword($password_hash);

            $em->flush();

            $this->addFlash('success', 'Votre nouveau mot de passe est ' . $new_password);
            return $this->redirectToRoute("app_security_forgot_password");
        }
        return $this->render('forgot_password/generate-new-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
