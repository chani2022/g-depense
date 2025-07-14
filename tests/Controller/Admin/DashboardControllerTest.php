<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\MockObject\MockObject;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\DomCrawler\Crawler;

class DashboardControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;
    use UserAuthenticatedTrait;

    /** @var MockObject&KernelBrowser&null*/
    private $client;
    private ?Crawler $crawler;
    private array|null $all_fixtures;

    private ?DashboardController $dashboardController;
    private ?User $userSimpleAuthenticated;
    private ?User $adminAuthenticated;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->all_fixtures = $this->getFixtures();

        $uploaderHelper = $this->getContainer()->get(UploaderHelper::class);
        $this->dashboardController = new DashboardController($uploaderHelper);
        $this->userSimpleAuthenticated = $this->getSimpeUserAuthenticated();
        $this->adminAuthenticated = $this->getAdminAuthenticated();
    }

    public function testIndexNotAccessUserAnonymous(): void
    {
        $this->client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/'); //redirection vers la login
    }

    public function testIndexDashboardSuccessfully(): void
    {
        $this->simulateAccessPageDashboardWithUser();
    }

    /**
     * @dataProvider configureUserMenuItems
     */
    public function testUserMenuDashboard(array $userMenuItems): void
    {
        $this->simulateAccessPageDashboardWithUser();
        $this->assertPageTitleContains('Dashboard');

        $this->assertUserMenuContaintsMenuItems($userMenuItems);
        $this->assertUserMenuDisplayUserAuthenticatedProperties();
    }

    /**
     * @dataProvider configureMenuItems
     */
    public function testMenuItemsDashboardWithUserAuthenticated(array $menuItems): void
    {
        $this->simulateAccessPageDashboardWithUser();

        if ($menuItems['label'] == 'User') {
            $this->assertSelectorNotExists('.' . $menuItems['css_classname']);
        } else {
            $this->assertSelectorTextContains('.' . $menuItems['css_classname'], $menuItems['label']);
        }
    }

    private function assertUserMenuDisplayUserAuthenticatedProperties(): void
    {
        /** @var DashboardController */
        $dashboardController = $this->getContainer()->get(DashboardController::class);
        $user = $this->getSimpeUserAuthenticated();
        $actualUser = $dashboardController->configureUserMenu($user);

        $this->assertEquals($user->getFullName(), $actualUser->getAsDto()->getName());
    }

    private function simulateAccessPageDashboardWithAdmin(): void
    {
        $adminLogged = $this->all_fixtures['user_admin'];
        $this->client->loginUser($adminLogged);
        $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageDashboardWithUser(): void
    {
        $userLogged = $this->all_fixtures['user_credentials_ok'];
        $this->client->loginUser($userLogged);
        $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    private function assertUserMenuContaintsMenuItems(array $menuItems): void
    {
        $this->assertSelectorTextContains('.' . $menuItems['css_classname'], $menuItems['label']);
    }

    public function testConfigureDashboard(): void
    {
        $dashboardActual = $this->dashboardController->configureDashboard();

        $this->assertEquals('Depense Mensuel', $dashboardActual->getAsDto()->getTitle());
    }

    public static function configureUserMenuItems(): array
    {
        return [
            [
                'profil' => [
                    'label' => 'My Profile',
                    'icon' => 'fa fa-id-card',
                    'route_name' => 'app_profil',
                    'css_classname' => 'profile'
                ]
            ],
            [
                'change password' => [
                    'label' => 'Change password',
                    'icon' => 'fa fa-id-card',
                    'route_name' => 'app_change_password',
                    'css_classname' => 'change-password'
                ]
            ],
            [
                'logout' => [
                    'label' => 'Logout',
                    'icon' => 'fa fa-logout',
                    'route_name' => 'app_logout',
                    'css_classname' => 'logout'
                ]
            ]
        ];
    }

    public static function configureMenuItems(): array
    {
        return [
            [
                'menu_items_access_user' => [
                    'label' => 'Dashboard',
                    'icon' => 'fa fa-home',
                    'css_classname' => 'dashboard'
                ]
            ],
            [
                'menu_item_denied_user' => [
                    'label' => 'User',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'crud-user'
                ]
            ],
            [
                'menu_items_access_user' => [
                    'label' => 'Compte salaire',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'compte-salaire'
                ]
            ],
        ];
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->dashboardController = null;
        $this->all_fixtures = null;
        $this->userSimpleAuthenticated = null;
        $this->adminAuthenticated = null;
        $this->crawler = null;
    }
}
