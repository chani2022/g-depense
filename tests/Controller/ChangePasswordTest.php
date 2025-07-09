<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Trait\LoadFixtureTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ChangePasswordTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?KernelBrowser $client;
    private ?Crawler $crawler;
    private ?User $userAuthenticated;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userAuthenticated = $this->getSimpeUserAuthenticated();
    }

    public function testPageChangePasswordNotAccessAnonymous(): void
    {
        $this->client->request('GET', '/change/password');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');
    }

    public function testPageChangePasswordExist(): void
    {
        $this->client->request('GET', '/change/password');

        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->crawler = null;
        $this->userAuthenticated = null;
    }
}
