<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    /**
     * @dataProvider formDataValid
     */
    public function testRegisterSuccess(array $formData): void
    {
        $this->submitForm($formData);
        //assert user
        /** @var User */
        $user = $this->getContainer()->get(UserRepository::class)->findOneByUsername($formData['registration_form']['username']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($formData['registration_form']['username'], $user->getUsername());

        //assert password
        /** @var UserPasswordHasherInterface */
        $hasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue(
            $hasher->isPasswordValid($user, $formData['registration_form']['password']['second'])
        );
    }
    /**
     * @dataProvider formDataInvalid
     */
    public function testRegisterFailed(array $formData, int $expected): void
    {
        $this->submitForm($formData);
        $actual = $this->crawler->filter('.invalid-feedback')->count();

        $this->assertEquals($expected, $actual);
    }

    private function submitForm(array $formData): void
    {
        $form = $this->crawler->selectButton('Enregistrer')->form($formData);

        $this->crawler = $this->client->submit($form);
        $this->client->followRedirects();
    }

    public static function formDataValid(): array
    {
        return [
            [
                [
                    'registration_form' => [
                        'username' => 'mon username',
                        'password' => [
                            'first' => 'exact',
                            'second' => 'exact'
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function formDataInValid(): array
    {
        return [
            'username_already_exist' => [
                [
                    'registration_form' => [
                        'username' => 'username',
                        'password' => [
                            'first' => 'exact',
                            'second' => 'exact'
                        ]
                    ]
                ],
                'expected' => 1
            ],
            'password_not_match' => [
                [
                    'registration_form' => [
                        'username' => 'other username',
                        'password' => [
                            'first' => 'exacte',
                            'second' => 'exact'
                        ]
                    ]
                ],
                'expected' => 1
            ],

            'password_not_match and username already exist' => [
                [
                    'registration_form' => [
                        'username' => 'username',
                        'password' => [
                            'first' => 'exacte',
                            'second' => 'exact'
                        ]
                    ]
                ],
                'expected' => 2
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->crawler = null;
    }
}
