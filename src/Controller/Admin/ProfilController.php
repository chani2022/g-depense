<?php

namespace App\Controller\Admin;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Flash\MessageFlash;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ProfilController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MessageFlash $messageFlash,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {

        // $adminUrlGenerator->setController()->setAction();
        // $redirectUrl = $adminUrlGenerator->generateUrl();

        $this->redirect($adminUrlGenerator->generateUrl());
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $user->setFile(null); //pour éviter la sérialisation
            $messageFlash->addFlash('success', 'Profil modifiée avec success');

            return $this->redirect($adminUrlGenerator->setRoute('app_profil')->generateUrl());
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
            // 'ea' => $adminContext
        ]);
    }
}
