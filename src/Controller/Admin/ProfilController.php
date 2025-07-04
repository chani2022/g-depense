<?php

namespace App\Controller\Admin;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Flash\MessageFlash;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class ProfilController extends AbstractDashboardController
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em,
        private MessageFlash $messageFlash,
        private AdminUrlGenerator $adminUrlGenerator
    ) {}
    #[IsGranted('ROLE_USER')]
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $user->setFile(null); //pour éviter la sérialisation
            $this->messageFlash->addFlash('success', 'Profil modifiée avec success');

            return $this->redirect($this->adminUrlGenerator->setRoute('app_profil')->generateUrl());
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName()
        ]);
    }
}
