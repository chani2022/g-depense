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

    public function testRegisterSuccess(): void
    {
        $uniqueUsernane = 'mon username';
        $plainPassword = 'exact';
        $form = $this->crawler->selectButton('Enregistrer')->form([
            'registration_form[username]' => $uniqueUsernane,
            'registration_form[password][first]' => $plainPassword,
            'registration_form[password][second]' => $plainPassword,
        ]);

        $this->client->submit($form);
        $this->client->followRedirects();
        //assert user
        /** @var User */
        $user = $this->getContainer()->get(UserRepository::class)->findOneByUsername($uniqueUsernane);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($uniqueUsernane, $user->getUsername());

        //assert password
        /** @var UserPasswordHasherInterface */
        $hasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue(
            $hasher->isPasswordValid($user, $plainPassword)
        );
    }



    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->crawler = null;
    }
}
