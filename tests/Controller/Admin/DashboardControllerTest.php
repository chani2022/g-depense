<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use Generator;
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
        $this->simulateCrudMenuItem($menuItem[1], 'User', 'fa fa-users', User::class);
    }

    private function simulateDashboardMenuItem(DashboardMenuItem $dashboardMenuItemActual, string $expectedLabel, string $expectedIcon): void
    {
        $this->assertInstanceOf(DashboardMenuItem::class, $dashboardMenuItemActual);
        $this->assertEquals($expectedLabel, $dashboardMenuItemActual->getAsDto()->getLabel());
        $this->assertEquals($expectedIcon, $dashboardMenuItemActual->getAsDto()->getIcon());
    }

    private function simulateCrudMenuItem(CrudMenuItem $crudMenuItemActual, string $expectedLabel, string $expectedIcon, string $expectedEntityFqcn): void
    {
        $this->assertInstanceOf(CrudMenuItem::class, $crudMenuItemActual);
        $this->assertEquals($expectedLabel, $crudMenuItemActual->getAsDto()->getLabel());
        $this->assertEquals($expectedIcon, $crudMenuItemActual->getAsDto()->getIcon());
        $this->assertEquals($expectedEntityFqcn, $crudMenuItemActual->getAsDto()->getRouteParameters()['entityFqcn']);
    }

    public function testConfigureUserMenu(): void
    {
        /** @var User */
        $userLogged = $this->all_fixtures['user_credentials_ok'];

        self::bootKernel(); // ou $this->createClient() si tu veux aussi tester la requête
        $container = self::getContainer();

        /** @var DashboardController $dashboardController */
        $dashboardController = $container->get(DashboardController::class);
        $userMenu = $dashboardController->configureUserMenu($userLogged);

        $this->assertEquals($userLogged->getFullName(), $userMenu->getAsDto()->getName());
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
