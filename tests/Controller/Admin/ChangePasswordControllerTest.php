<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Trait\UserAuthenticatedTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?KernelBrowser $client;
    private ?Crawler $crawler;
    private ?User $userSimpleAuthenticated;
    private ?User $adminAuthenticated;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userSimpleAuthenticated = $this->getSimpeUserAuthenticated();
        $this->adminAuthenticated = $this->getAdminAuthenticated();
    }

    public function testChangePasswordNotAccessUserAnonymous(): void
    {
        $this->client->request('GET', '/change/password');

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * @dataProvider userAuthorized()
     */
    public function testChangePasswordWithUserAuthorized(string $roles): void
    {
        $this->client->loginUser($roles == 'user' ? $this->userSimpleAuthenticated : $this->adminAuthenticated);

        $this->client->request('GET', '/change/password');

        $this->assertResponseIsSuccessful();
    }
    /**
     * @dataProvider formDataValid
     */
    public function testSubmitFormWithValidData(array $formData): void
    {
        $this->simulateAccesPageChangePassword();

        $expectedNewPassword = $formData['change_password']['newPassword']['second'];
        $this->simulateSubmitFormChangePassword($formData);

        $this->client->followRedirects();
        $this->assertResponseRedirects('/');

        $this->assertUserPasswordChange($expectedNewPassword);
    }

    /**
     * @dataProvider formDataInvalid
     */
    public function testSubmitFormWithInvalidData(array $formData, int $nbErrorsFormExpected): void
    {
        $this->simulateAccesPageChangePassword();

        $this->simulateSubmitFormChangePassword($formData);

        $nbErrorsActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($nbErrorsFormExpected, $nbErrorsActual);
    }

    private function simulateAccesPageChangePassword(): void
    {
        $this->client->loginUser($this->userSimpleAuthenticated);
        /** @var Crawler */
        $this->crawler = $this->client->request('GET', '/change/password');
    }

    private function assertUserPasswordChange(string $expectedNewPassword): void
    {
        /** @var UserRepository */
        $userRepository = $this->getContainer()->get(UserRepository::class);
        /** @var UserPasswordHasherInterface */
        $hasher = $this->getContainer()->get(UserPasswordHasherInterface::class);

        /** @var User */
        $user = $userRepository->findOneByUsername($this->userSimpleAuthenticated->getUsername());
        $this->assertTrue(
            $hasher->isPasswordValid($user, $expectedNewPassword)
        );
    }

    private function simulateSubmitFormChangePassword(array $formData)
    {
        $form = $this->crawler->selectButton('Modifier')->form($formData);
        $this->crawler = $this->client->submit($form);
    }
    /**
     * return array<array, {array {string, array {string, string }}}>
     */
    public static function formDataValid(): array
    {
        return [
            [
                [
                    'change_password' => [
                        'oldPassword' => 'password',
                        'newPassword' => [
                            'first' => 'my new password',
                            'second' => 'my new password'
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function formDataInValid(): array
    {
        return [
            '2 mot de passe not mismatch' => [
                [
                    'change_password' => [
                        'oldPassword' => 'password',
                        'newPassword' => [
                            'first' => 'my password',
                            'second' => 'my new password'
                        ]
                    ]
                ],
                'nbErrorsFormExpected' => 1
            ],
            'old password wrong' => [
                [
                    'change_password' => [
                        'oldPassword' => 'wrong password',
                        'newPassword' => [
                            'first' => 'my new password',
                            'second' => 'my new password'
                        ]
                    ]
                ],
                'nbErrorsFormExpected' => 1
            ],
            '2 mot de passe not mismatch and old password wrong' => [
                [
                    'change_password' => [
                        'oldPassword' => 'wrong password',
                        'newPassword' => [
                            'first' => 'my password',
                            'second' => 'my new password'
                        ]
                    ]
                ],
                'nbErrorsFormExpected' => 2
            ],
        ];
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
        $this->userSimpleAuthenticated = null;
        $this->adminAuthenticated = null;
    }
}
