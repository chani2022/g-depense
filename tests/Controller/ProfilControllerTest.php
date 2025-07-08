<?php

namespace App\Tests\Controller;

use App\Tests\Trait\LoadFixtureTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ProfilControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testPageProfilNotAccessIfUserIsAnonymous(): void
    {
        $this->client->request('GET', '/profil');

        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * @dataProvider userAuthorized
     */
    public function testAccessProfilePageOkWithUserValid(string $roles): void
    {
        $authenticatedUser = $this->getFixtures()['user_credentials_ok'];
        if ($roles == 'admin') {
            $authenticatedUser = $this->getFixtures()['user_admin'];
        }
        $this->client->loginUser($authenticatedUser);

        $this->client->request('GET', '/profil');
        $this->assertResponseIsSuccessful();
    }

    public function testSubmitFormSuccess(array $formData): void {}

    public static function userAuthorized(): array
    {
        return [
            ['user'],
            ['admin']
        ];
    }

    public function formDataValid(): array
    {
        return [
            [
                'profil' => [
                    'nom' => 'nom',
                    'prenom' => 'prenom',
                    'username' => 'mon username',
                    'file' => [
                        'file' => ''
                    ]
                ]
            ]
        ];
    }
}
