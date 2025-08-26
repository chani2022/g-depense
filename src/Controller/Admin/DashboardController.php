<?php

namespace App\Controller\Admin;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Entity\Depense;
use App\Entity\Unite;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfilType;
use App\Form\SearchDepenseType;
use App\Repository\DepenseRepository;
use App\Ux\HandleDepense;
use App\Ux\MyChart;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UploaderHelper $uploaderHelper,
        private DepenseRepository $depenseRepository,
        private RequestStack $requestStack,
        private HandleDepense $handleDepense
    ) {}

    /**
     * Representation graphique du depense par rapport au capital pour 
     * chaque compte salaire d'un utilisateur
     * 
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $dates = [];
        $formSearch = $this->createForm(SearchDepenseType::class);
        $formSearch->handleRequest($this->requestStack->getCurrentRequest());
        if ($formSearch->isSubmitted()) {
            $dates = $formSearch->get('dates')->getData();
            $dates = explode(' - ', $dates);
        }

        $depenseAndCapitalMensuels = $this->depenseRepository->findDepensesWithCapital($this->getUser(), $dates);

        $labels = $this->handleDepense->getLabels($depenseAndCapitalMensuels);
        $datasets = $this->handleDepense->getDatasets($depenseAndCapitalMensuels);

        $depenseByCompteSalaire = (new MyChart('horizontal-bar', 'Comparaison de depense et capital.'))
            ->setData([
                'labels' => $labels,
                'datasets' => $datasets
            ]);

        return $this->render('admin/dashboard.html.twig', [
            'chart_depense_compte_salaire' => $depenseByCompteSalaire->getChart(),
            'form' => $formSearch->createView()
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Depense Mensuel')
            ->setLocales([
                'en',
                Locale::new('fr', 'Français', 'fas fa-flag'),
                Locale::new('de', 'Deutsch', 'fas fa-flag'),
            ]);
    }
    /**
     * @param User $user
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user)
            ->setName($user->getFullName());
        if ($user->getImageName()) {
            $userMenu->setAvatarUrl($this->uploaderHelper->asset($user, 'file'));
        }

        return $userMenu->addMenuItems([
            MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'app_profil')->setPermission('ROLE_USER')->setCssClass('profile'),
            MenuItem::linkToRoute('Change password', 'fa fa-id-card', 'app_change_password')->setPermission('ROLE_USER')->setCssClass('change-password'),
        ]);
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home')
                ->setCssClass('dashboard'),
            MenuItem::linkToCrud('User', 'fa fa-users', User::class)
                ->setCssClass('crud-user')
                ->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Compte salaire', 'fa fa-calendar', CompteSalaire::class)
                ->setCssClass('compte-salaire'),
            MenuItem::linkToCrud('Capital', 'fa fa-money', Capital::class)
                ->setCssClass('capital'),
            MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class)
                ->setCssClass('categories'),
            MenuItem::linkToCrud('Unite', 'fa fa-icons', Unite::class)
                ->setCssClass('unite'),
            MenuItem::linkToCrud('Depense', 'fa fa-comments-dollar', Depense::class)
                ->setCssClass('depense')
        ];
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->flush();
                $user->setFile(null); //pour eviter le serialization du fichier
                return $this->redirectToRoute('app_profil');
            } else {
                $user->setFile(null); //pour eviter le serialization du fichier
                //pour ne pas afficher le nom et prenom dans l'affichage du profil easyadmin
                $em->refresh($user);
                $em->clear();
            }
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/change/password', name: 'app_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainNewPassword = $form->get('newPassword')->getData();
            $user->setPassword(
                $hasher->hashPassword(new User(), $plainNewPassword)
            );

            $em->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('change_password/change-password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function configureAssets(): Assets
    {
        return (Assets::new())
            ->addCssFile('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css')
            ->addHtmlContentToHead(
                '
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js" defer></script>
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" defer></script>
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
                    <script type="text/javascript" src="js/searchDepense.js" defer></script>
                '
            );
    }
}
