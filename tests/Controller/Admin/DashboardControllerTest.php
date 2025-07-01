<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Trait\LoadFixtureTrait;
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

    public function testPageIndexExist(): void
    {
        $userLogged = $this->all_fixtures['user_credentials_ok'];
        $this->client->loginUser($userLogged);
        $this->client->request('GET', '/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Dashboard');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
