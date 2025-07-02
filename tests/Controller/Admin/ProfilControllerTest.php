<?php

namespace App\Tests\Controller\Admin;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    private KernelBrowser|null $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testProfilSuccess(): void
    {
        // this examples doesn't use security; in your application you may
        // need to ensure that the user is logged before the test
        $this->client->request("GET", '/profil');
        static::assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
