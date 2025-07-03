<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\RouteMenuItem;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\MockObject\MockObject;

class DashboardControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;

    /** @var MockObject&KernelBrowser&null*/
    private $client;

    private array|null $all_fixtures;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->all_fixtures = $this->getFixtures();
    }

    public function testPageIndexDashboardExist(): void
    {
        $userLogged = $this->all_fixtures['user_credentials_ok'];
        $this->client->loginUser($userLogged);
        $this->client->request('GET', '/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Dashboard');
    }

    public function testConfigureDashboard(): void
    {
        $dashboardController = new DashboardController();
        $dashboardActual = $dashboardController->configureDashboard();

        $this->assertEquals('Depense Mensuel', $dashboardActual->getAsDto()->getTitle());
    }

    public function testConfigureMenuItems(): void
    {
        $dashboardController = new DashboardController();
        $menuItem = $dashboardController->configureMenuItems();

        $this->assertCount(2, $menuItem);
        $this->simulateDashboardMenuItem($menuItem[0], 'Dashboard', 'fa fa-home');
        $this->simulateCrudMenuItem($menuItem[1], 'User', 'fa fa-users', User::class, 'ROLE_ADMIN');
    }

    private function simulateDashboardMenuItem(DashboardMenuItem $dashboardMenuItemActual, string $expectedLabel, string $expectedIcon): void
    {
        $this->assertInstanceOf(DashboardMenuItem::class, $dashboardMenuItemActual);
        $this->assertEquals($expectedLabel, $dashboardMenuItemActual->getAsDto()->getLabel());
        $this->assertEquals($expectedIcon, $dashboardMenuItemActual->getAsDto()->getIcon());
    }

    private function simulateCrudMenuItem(
        CrudMenuItem $crudMenuItemActual,
        string $expectedLabel,
        string $expectedIcon,
        string $expectedEntityFqcn,
        string $expectedPermission = ''
    ): void {
        $this->assertInstanceOf(CrudMenuItem::class, $crudMenuItemActual);
        $this->assertEquals($expectedLabel, $crudMenuItemActual->getAsDto()->getLabel());
        $this->assertEquals($expectedIcon, $crudMenuItemActual->getAsDto()->getIcon());
        $this->assertEquals($expectedEntityFqcn, $crudMenuItemActual->getAsDto()->getRouteParameters()['entityFqcn']);
        $this->assertEquals($expectedPermission, $crudMenuItemActual->getAsDto()->getPermission());
    }

    public function testConfigureUserMenu(): void
    {
        /** @var User */
        $userLogged = $this->all_fixtures['user_credentials_ok'];

        self::bootKernel();
        $container = self::getContainer();

        /** @var DashboardController $dashboardController */
        $dashboardController = $container->get(DashboardController::class);
        $userMenu = $dashboardController->configureUserMenu($userLogged);

        $items = $userMenu->getAsDto()->getItems();

        $this->assertEquals($userLogged->getFullName(), $userMenu->getAsDto()->getName());
        $this->assertTrue($userMenu->getAsDto()->isAvatarDisplayed());
        $this->assertTrue($userMenu->getAsDto()->getItems() > 0);

        $this->simulateItemUserMenuLinkToRouteProfil($items[0]);
        $this->simulateItemUserMenuLinkToRouteChangePassword($items[1]);
    }

    private function simulateItemUserMenuLinkToRouteProfil(RouteMenuItem $menuItemProfilActual): void
    {
        $dto = $menuItemProfilActual->getAsDto();
        $this->assertInstanceOf(RouteMenuItem::class, $menuItemProfilActual);
        $this->assertEquals("app_profil", $dto->getRouteName());
        $this->assertEquals('ROLE_USER', $dto->getPermission());
    }

    private function simulateItemUserMenuLinkToRouteChangePassword(RouteMenuItem $menuItemPasswordActual): void
    {
        $dto = $menuItemPasswordActual->getAsDto();
        $this->assertInstanceOf(RouteMenuItem::class, $menuItemPasswordActual);
        $this->assertEquals("app_change_password", $dto->getRouteName());
        $this->assertEquals('ROLE_USER', $dto->getPermission());
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
