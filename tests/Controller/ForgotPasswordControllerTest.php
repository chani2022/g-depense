<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ForgotPasswordControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;

    private KernelBrowser|null $client;
    private EntityManagerInterface|null $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testGenerateNewPasswordSuccess(): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/forgot/password');
        $this->assertResponseIsSuccessful();
        $username = 'username';
        $form = $crawler->selectButton('forgot_password[envoyer]')->form([
            'forgot_password[username]' => $username
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');

        $this->simulateNewPassword($username);
    }

    private function simulateNewPassword(string $username): void
    {
        /** @var UserRepository */
        $userRepository = $this->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy([
            'username' => $username
        ]);

        $new_password = 'test';
        /** @var UserPasswordHasherInterface */
        $hasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $expected = $hasher->isPasswordValid($user, $new_password);
        $this->assertTrue($expected);
    }

    public function testGenerateNewPasswordUsernameNotFound(): void
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/forgot/password');
        $this->assertResponseIsSuccessful();
        $username = 'fake username';
        $form = $crawler->selectButton('forgot_password[envoyer]')->form([
            'forgot_password[username]' => $username
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->em = null;
    }
}
