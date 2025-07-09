<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ChangePasswordTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?KernelBrowser $client;
    private ?Crawler $crawler;
    private ?User $userAuthenticated;
    private ?User $adminAuthenticated;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userAuthenticated = $this->getSimpeUserAuthenticated();
        $this->adminAuthenticated = $this->getAdminAuthenticated();
    }

    public function testPageChangePasswordNotAccessAnonymous(): void
    {
        $this->client->request('GET', '/change/password');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');
    }
    /**
     * @dataProvider userAuthorized
     */
    public function testUserAccessPageChangePasswordAuthorized(string $roles): void
    {
        $this->client->loginUser($roles == 'user' ? $this->userAuthenticated : $this->adminAuthenticated);
        $this->client->request('GET', '/change/password');
        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $urlActual = $response->getTargetUrl();

        $this->assertResponseStatusCodeSame(302);
        $this->assertStringContainsString('/admin?crudAction=changePassword', $urlActual);
        $this->client->request('GET', $urlActual);
        // $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('changement mot de passe');
    }

    /**
     * @return string[][]
     */
    public static function userAuthorized(): array
    {
        return [
            ['user'],
            ['admin']
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->crawler = null;
        $this->userAuthenticated = null;
        $this->adminAuthenticated = null;
    }
}
