<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Service\GeneratorNewPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    const NEW_PASSWORD = 'test';

    #[Route('/forgot/password', name: 'app_security_forgot_password')]
    public function generateNewPassword(Request $request, EntityManagerInterface $em, GeneratorNewPassword $generatorNewPassword): Response
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

            $generatorNewPassword
                ->regeneratePwd($user, self::NEW_PASSWORD)
                ->save();

            $this->addFlash('success', 'Votre nouveau mot de passe est ' . self::NEW_PASSWORD);
            return $this->redirectToRoute("app_security_forgot_password");
        }

        return $this->render('forgot_password/generate-new-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
