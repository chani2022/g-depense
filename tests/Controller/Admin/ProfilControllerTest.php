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
    private User|null $userToLogged;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
        $this->path_uploaded_file = $this->getContainer()->getParameter('path_uploaded_image_users');
        $this->userToLogged = $this->getFixtures()['user_credentials_ok'];
    }

    public function testPageProfilExist(): void
    {
        $this->simulateAccessPageProfil();

        static::assertResponseIsSuccessful();
        $this->assertPageTitleSame('Profil');
    }
    /**
     * @dataProvider extensionValid
     */
    public function testProfilSuccess(string $filename, string $mimeType): void
    {
        $crawler = $this->simulateAccessPageProfil();

        $pathMock = $this->simulateSubmitForm($crawler, $filename, $mimeType);

        /** @var User */
        $user = $this->userRepository->find($this->userToLogged->getId());

        $this->assertEquals('NOM', $user->getNom());
        $this->assertEquals('Prenom', $user->getPrenom());
        $this->assertEquals('username', $user->getUsername());
        $this->assertFileExists($this->pathFileUploaded($user));

        unlink($this->pathFileUploaded($user));
        unlink($pathMock);
    }

    private function simulateSubmitForm(Crawler $crawler, string $filename, string $mimeType): string
    {
        $pathMock = $this->mockFile($filename);

        $form = $crawler->selectButton('Modifier')->form();
        $form['profil[nom]'] = 'nom';
        $form['profil[prenom]'] = 'prenom';
        $form['profil[username]'] = 'username';

        $uplodedFile = new UploadedFile($pathMock, $filename, $mimeType, null, true);
        $form['profil[file][file]'] = $uplodedFile;

        $this->client->submit($form);

        return $pathMock;
    }

    private function simulateAccessPageProfil(): Crawler
    {
        $this->client->loginUser($this->userToLogged);
        return $this->client->request("GET", '/profil');
    }

    private function pathFileUploaded(User $user): string
    {
        return $this->path_uploaded_file . DIRECTORY_SEPARATOR . $user->getImageName();
    }

    private function mockFile(string $filename): string
    {
        $callableCreateImage = function ($image, $path) use ($filename) {
            $extension = explode('.', $filename)[1];
            match ($extension) {
                'jpeg' => imagejpeg($image, $path),
                'png' => imagepng($image, $path)
            };
            imagedestroy($image);
        };

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $image = imagecreatetruecolor(10, 10); // 10x10 pixels
        $callableCreateImage($image, $path);

        return $path;
    }
    /**
     * @return array<array{string, string}>>
     */
    public static function extensionValid(): array
    {
        return [
            ['filename' => 'test.jpeg', 'mimeType' => 'image/jpeg'],
            ['filename' => 'test.png', 'mimeType' => 'image/png']
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->userRepository = null;
        $this->userToLogged = null;
        $this->path_uploaded_file = null;
    }
}
