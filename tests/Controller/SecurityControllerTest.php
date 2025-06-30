<?php

namespace App\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SecurityControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    private KernelBrowser|null $client;

    protected function setUp(): void
    {
        parent::setUp();
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
        $this->client->followRedirect();
        $this->assertPageTitleContains("Dashboard");
    }
    /**
     * @dataProvider getBadCredentials
     */
    public function testLoginBadCredentials(array $badCredentials): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('se connecter')
            ->form($badCredentials);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert');
    }

    public static function getBadCredentials(): array
    {
        return [
            'username fake' => [
                [
                    'username' => 'fake username',
                    'password' => 'password'
                ]
            ],
            'password fake' => [
                [
                    'username' => 'username',
                    'password' => 'fake password'
                ]
            ],
            'username et password fake' => [
                [
                    'username' => 'fake username',
                    'password' => 'fake password'
                ]
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
