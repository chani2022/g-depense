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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractDashboardController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, EntityManagerInterface $em, UserProviderInterface $userProvider): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $user->setFile(null); //pour eviter le serialization du fichier
            if ($form->isValid()) {
                $em->flush();
                return $this->redirectToRoute('app_profil');
            } else {
                //pour ne pas afficher le nom et prenom dans l'affichage du profil easyadmin
                $em->refresh($user);
                $em->clear();
            }
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
