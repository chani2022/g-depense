<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\RouteMenuItem;
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
        $this->simulateAccessPageDashboard();
    }

    /**
     * @dataProvider configureUserMenuItems
     */
    public function testUserMenuDashboard(array $userMenuItems): void
    {
        $this->simulateAccessPageDashboard();
        $this->assertPageTitleContains('Dashboard');

        $this->assertUserMenuContaintsMenuItems($userMenuItems);
        $this->assertUserMenuDisplayUserAuthenticatedProperties();
    }

    /**
     * @dataProvider configureMenuItems
     */
    public function testMenuItemsDashboard(array $menuItems): void
    {
        $this->simulateAccessPageDashboard();

        $this->assertSelectorTextContains('.' . $menuItems['css_classname'], $menuItems['label']);
    }

    private function assertUserMenuDisplayUserAuthenticatedProperties(): void
    {
        /** @var DashboardController */
        $dashboardController = $this->getContainer()->get(DashboardController::class);
        $user = $this->getSimpeUserAuthenticated();
        $actualUser = $dashboardController->configureUserMenu($user);

        $this->assertEquals($user->getFullName(), $actualUser->getAsDto()->getName());
    }

    private function simulateAccessPageDashboard(): void
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

    // private function assertMenuItemsExist(array $menuItems): void
    // {
    //     $this->assertSelectorTextContains('.' . $menuItems['css_classname'], $menuItems['label']);
    // }

    // public function testConfigureMenuItems(): void
    // {
    //     $menuItem = $this->dashboardController->configureMenuItems();

    //     $this->assertCount(2, $menuItem);
    //     $this->simulateDashboardMenuItem($menuItem[0], 'Dashboard', 'fa fa-home');
    //     $this->simulateCrudMenuItem($menuItem[1], 'User', 'fa fa-users', User::class, 'ROLE_ADMIN');
    // }

    // private function simulateDashboardMenuItem(
    //     DashboardMenuItem $dashboardMenuItemActual,
    //     string $expectedLabel,
    //     string $expectedIcon
    // ): void {
    //     $this->assertInstanceOf(DashboardMenuItem::class, $dashboardMenuItemActual);
    //     $this->assertEquals($expectedLabel, $dashboardMenuItemActual->getAsDto()->getLabel());
    //     $this->assertEquals($expectedIcon, $dashboardMenuItemActual->getAsDto()->getIcon());
    // }

    // private function simulateCrudMenuItem(
    //     CrudMenuItem $crudMenuItemActual,
    //     string $expectedLabel,
    //     string $expectedIcon,
    //     string $expectedEntityFqcn,
    //     string $expectedPermission = ''
    // ): void {
    //     $this->assertInstanceOf(CrudMenuItem::class, $crudMenuItemActual);
    //     $this->assertEquals($expectedLabel, $crudMenuItemActual->getAsDto()->getLabel());
    //     $this->assertEquals($expectedIcon, $crudMenuItemActual->getAsDto()->getIcon());
    //     $this->assertEquals($expectedEntityFqcn, $crudMenuItemActual->getAsDto()->getRouteParameters()['entityFqcn']);
    //     $this->assertEquals($expectedPermission, $crudMenuItemActual->getAsDto()->getPermission());
    // }

    // public function testConfigureUserMenu(): void
    // {
    //     /** @var User */
    //     $authenticatedUser = $this->all_fixtures['user_credentials_ok'];

    //     self::bootKernel();
    //     $container = self::getContainer();

    //     /** @var DashboardController $dashboardController */
    //     $this->dashboardController = $container->get(DashboardController::class);
    //     $userMenu = $this->dashboardController->configureUserMenu($authenticatedUser);

    //     $this->assertEquals($authenticatedUser->getFullName(), $userMenu->getAsDto()->getName());
    //     $this->assertTrue($userMenu->getAsDto()->isAvatarDisplayed());
    //     $this->assertTrue($userMenu->getAsDto()->getItems() > 0);

    //     /** simulation de menu item */
    //     $items = $userMenu->getAsDto()->getItems();
    //     $this->simulateItemUserMenuLinkToRouteProfil($items[0]);
    //     $this->simulateItemUserMenuLinkToRouteChangePassword($items[1]);
    //     $this->simulateItemUserMenuLinkToRouteLogout($items[2]);
    // }

    // private function simulateItemUserMenuLinkToRouteProfil(RouteMenuItem $menuItemProfilActual): void
    // {
    //     $dto = $menuItemProfilActual->getAsDto();
    //     $this->assertInstanceOf(RouteMenuItem::class, $menuItemProfilActual);
    //     $this->assertEquals("app_profil", $dto->getRouteName());
    //     $this->assertEquals('ROLE_USER', $dto->getPermission());
    // }

    // private function simulateItemUserMenuLinkToRouteChangePassword(RouteMenuItem $menuItemPasswordActual): void
    // {
    //     $dto = $menuItemPasswordActual->getAsDto();
    //     $this->assertInstanceOf(RouteMenuItem::class, $menuItemPasswordActual);
    //     $this->assertEquals("app_change_password", $dto->getRouteName());
    //     $this->assertEquals('ROLE_USER', $dto->getPermission());
    // }

    // private function simulateItemUserMenuLinkToRouteLogout(RouteMenuItem $menuItemLogout): void
    // {
    //     $dto = $menuItemLogout->getAsDto();
    //     $this->assertEquals('app_logout', $dto->getRouteName());
    // }

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
                [
                    'label' => 'Dashboard',
                    'icon' => 'fa fa-home',
                    'css_classname' => 'dashboard'
                ]
            ],
            [
                [
                    'label' => 'User',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'crud-user'
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
