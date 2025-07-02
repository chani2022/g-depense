<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use App\Tests\Trait\LoadFixtureTrait;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;

class ProfilControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;

    private KernelBrowser|null $client;
    private UserRepository|null $userRepository;
    private string|null $path_uploaded_file;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
        $this->path_uploaded_file = $this->getContainer()->getParameter('path_uploaded_image_users');
    }

    public function testProfilSuccess(): void
    {
        /** @var User */
        $userLogged = $this->getFixtures()['user_credentials_ok'];
        /** @var Crawler */
        $crawler = $this->client->loginUser($userLogged);

        $this->client->request("GET", '/profil');
        static::assertResponseIsSuccessful();

        $pathMock = $this->mockFile();

        $form = $crawler->selectButton('Modifier')->form([
            'profil[nom]' => 'nom',
            'profil[prenom]' => 'prenom',
            // 'profil[username]' => 'mon username',
            'profil[file]' => new UploadedFile($pathMock, 'test.png', 'images/png', null, true)
        ]);

        $this->client->submit($form);
        /** @var User */
        $user = $this->userRepository->find($userLogged->getId());

        $this->assertEquals('NOM', $user->getNom());
        $this->assertEquals('Prenom', $user->getPrenom());
        $this->assertEquals('username', $user->getUsername());
        $this->assertFileExists($this->pathFileUploaded($user));

        unlink($this->path_uploaded_file);
        unlink($pathMock);
    }

    private function pathFileUploaded(User $user): string
    {
        return $this->path_uploaded_file . DIRECTORY_SEPARATOR . $user->getImageName();
    }

    private function mockFile(): string
    {
        $filename = 'test.png';
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, 'fake png');

        return $path;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }
}
