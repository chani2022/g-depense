<?php

namespace App\Controller\Admin;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Flash\MessageFlash;

class ProfilController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em, MessageFlash $messageFlash): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $messageFlash->addFlash('success', 'Profil modifiée avec success');
            return $this->redirectToRoute("app_profil");
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
