<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class RegistrationControllerTest extends WebTestCase
{

    private ?KernelBrowser $client;
    private ?Crawler $crawler;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->crawler = $this->client->request('GET', '/register');
    }

    public function testPageRegisterExist(): void
    {
        $this->assertResponseIsSuccessful();
    }



    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->crawler = null;
    }
}
