<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Repository\DepenseRepository;
use App\Tests\Trait\LoadFixtureTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use App\Ux\HandleDepense;
use App\Ux\MyChart;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\MockObject\MockObject;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $depenseRepository = $this->getContainer()->get(DepenseRepository::class);
        $requestStack = $this->getContainer()->get(RequestStack::class);
        $handleDepense = new HandleDepense();

        $this->dashboardController = new DashboardController($uploaderHelper, $depenseRepository, $requestStack, $handleDepense);
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

        // $this->assertSelectorExists('.depense-compte-salaire');

        // $this->assertSelectorExists('form[name=search_depense]');

        // $dataChart = $this->crawler->filter('.depense-compte-salaire')->attr('data-symfony--ux-chartjs--chart-view-value');
        // $dataChart = json_decode($dataChart, true);
        // dd($dataChart);
    }

    /**
     * @dataProvider providerDataChartDefault
     */
    public function testAssertDataChartDefaultDate(array $dataChartExpected): void
    {
        $this->simulateAccessPageDashboardWithUser();

        $dataChartActual = $this->simulateDataChart();

        $this->assertSame($dataChartExpected, $dataChartActual);
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

    /**
     * @dataProvider configureMenuItems
     */
    public function testMenuItemsDashboardWithAdminAuthenticated(array $menuItems): void
    {
        $this->simulateAccessPageDashboardWithAdmin();

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

    private function simulateAccessPageDashboardWithAdmin(): void
    {
        $adminLogged = $this->all_fixtures['user_admin'];
        $this->client->loginUser($adminLogged);
        $this->crawler = $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.depense-compte-salaire');
    }

    private function simulateAccessPageDashboardWithUser(): void
    {
        $userLogged = $this->all_fixtures['user_credentials_ok'];
        $this->client->loginUser($userLogged);
        $this->crawler = $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    private function simulateDataChart(): array
    {
        $dataChart = $this->crawler->filter('.depense-compte-salaire')->attr('data-symfony--ux-chartjs--chart-view-value');
        $dataChart = json_decode($dataChart, true);

        return $dataChart;
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
        ];
    }

    public static function configureMenuItems(): array
    {
        return [
            [
                'menu_items_dashboard_access_user' => [
                    'label' => 'Dashboard',
                    'icon' => 'fa fa-home',
                    'css_classname' => 'dashboard'
                ]
            ],
            [
                'menu_item_user_denied_user' => [
                    'label' => 'User',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'crud-user'
                ]
            ],
            [
                'menu_items_compte_salaire_access_user' => [
                    'label' => 'Compte salaire',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'compte-salaire'
                ]
            ],
            [
                'menu_items_captial_access_user' => [
                    'label' => 'Capital',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'capital'
                ],

            ],
            [
                'menu_items_category_access_user' => [
                    'label' => 'Categories',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'categories'
                ],

            ],
            [
                'menu_items_quantity_access_user' => [
                    'label' => 'Unite',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'unite'
                ],
            ],
            [
                'menu_items_depense_access_user' => [
                    'label' => 'Depense',
                    'icon' => 'fa fa-users',
                    'css_classname' => 'depense'
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{string, string|float}>
     */
    public static function providerDataChartDefault(): array
    {
        return [
            'dataExpected' => [
                [
                    'type' => 'bar',
                    'data' => [
                        'labels' => [(new \DateTime('-7 days'))->format('d/m/Y') . ' - ' . (new \DateTime('+7 days'))->format('d/m/Y')],
                        'datasets' => [
                            [
                                'label' => 'Depense mensuel',
                                'data' => [25.25],
                                'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['border'],
                                'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['background'],
                            ],
                            [
                                'label' => 'Capital mensuel',
                                'data' => [15.25],
                                'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['border'],
                                'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['background'],
                            ]
                        ]
                    ],
                    'options' => [
                        'responsive' => true,
                        'scales' => [
                            'x' => [
                                'suggestedMin' => 0,
                                'suggestedMax' => 100
                            ]
                        ],
                        'indexAxis' => 'y',
                        'plugins' => [
                            'title' => [
                                'display' => true,
                                'text' => 'Comparaison de depense et capital.'
                            ]
                        ]
                    ]
                ]
            ]
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
