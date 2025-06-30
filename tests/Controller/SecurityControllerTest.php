<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testPageLoginExist(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginSuccess(): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('se connecter')
            ->form([
                'username' => 'username',
                'password' => 'password'
            ]);

        $this->client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->client->followRedirect();
        $this->assertPageTitleContains("Dashboard");
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }
}
