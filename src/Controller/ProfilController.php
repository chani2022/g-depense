<?php

namespace App\Controller;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractDashboardController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $user->setFile(null); //pour eviter le serialization du fichier
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
