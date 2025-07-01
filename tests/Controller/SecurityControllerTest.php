<?php

namespace App\Tests\Controller;

use App\Tests\Trait\LoadFixtureTrait;;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SecurityControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;

    private KernelBrowser|null $client;
    protected array|null $all_fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->all_fixtures = $this->getFixtures();
    }

    public function testPageLoginExist(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginSuccess(): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Se connecter')
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
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Se connecter')
            ->form($badCredentials);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert');
    }

    public function testRememberMeSetsCookieAndAutoLogin(): void
    {
        $this->client->followRedirects(false);
        $this->client->getCookieJar()->clear(); // on part d’un jar vierge

        // 1) On récupère le formulaire de login
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([
            'username'     => 'username',
            'password'     => 'password',
            'remember_me'  => 'on',  // coche la case "se souvenir de moi"
        ]);

        // 2) On soumet sans suivre la redirection pour inspecter les headers
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);

        // 3) Vérifie que le cookie REMEMBERME est présent
        $cookie = $this->client->getCookieJar()->get('REMEMBERME');
        $this->assertNotNull($cookie, 'Le cookie REMEMBERME doit être défini');
        $this->assertGreaterThan(time(), $cookie->getExpiresTime());

        $this->tearDown(); //initialisation du client
        // $this->setUp(); // on recrée le client
        $this->client = static::createClient();
        // 4) Simule une nouvelle visite en réutilisant le cookie
        $this->client->getCookieJar()->clear();      // start clean
        $this->client->getCookieJar()->set($cookie); // injecte le cookie REMEMBERME

        // 5) Accès à une page protégée sans se loguer explicitement
        $this->client->request('GET', '/dashboard');
        $this->assertResponseIsSuccessful();
    }

    public function testClickLinkForgotPasswordSuccess(): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/');
        $filter_link = $crawler->filter('#forgot-password');
        $number_link = $filter_link->count();
        $link = $filter_link->link();

        $this->assertEquals($number_link, 1);
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
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

    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertPageTitleSame('Connexion');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->all_fixtures = null;
    }
}
