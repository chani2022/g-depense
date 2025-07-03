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

    private ?KernelBrowser $client;
    private ?UserRepository $userRepository;
    private ?string $pathUploadedFile;
    private ?User $userToLogged;
    private ?array $temporaryFiles;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
        $this->pathUploadedFile = $this->getContainer()->getParameter('path_uploaded_image_users');
        $this->userToLogged = $this->getFixtures()['user_credentials_ok'];
        $this->temporaryFiles = [];
    }

    public function testPageProfilExist(): void
    {
        $this->simulateAccessPageProfil();

        static::assertResponseIsSuccessful();
        $this->assertPageTitleSame('Profil');
    }
    /**
     * @dataProvider dataFormValidWithFileValid
     */
    public function testProfilSuccess(array $dataForm): void
    {
        $crawler = $this->simulateAccessPageProfil();

        $pathMock = $this->simulateSubmitForm($crawler, $dataForm);

        /** @var User */
        $user = $this->userRepository->find($this->userToLogged->getId());
        $this->temporaryFiles[] = $pathMock;
        $this->temporaryFiles[] = $this->pathFileUploaded($user);

        $this->assertEquals('NOM', $user->getNom());
        $this->assertEquals('Prenom', $user->getPrenom());
        $this->assertEquals('username', $user->getUsername());
        $this->assertFileExists($this->pathFileUploaded($user));
    }

    private function simulateSubmitForm(Crawler $crawler, array $dataForm): string
    {
        $filename = $dataForm['profil']['file_info']['filename'];
        $mimeType = $dataForm['profil']['file_info']['mimeType'];
        unset($dataForm['profil']['file_info']); //on supprime car c'est unitule dans le form et ça provoque une erreur

        $pathMock = $this->mockFileValid($filename);

        $uploadedFile = new UploadedFile($pathMock, $filename, $mimeType, null, true);
        $dataForm['profil']['file']['file'] = $uploadedFile;
        $form = $crawler->selectButton('Modifier')->form($dataForm);
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
        return $this->pathUploadedFile . DIRECTORY_SEPARATOR . $user->getImageName();
    }

    private function mockFileValid(string $filename): string
    {
        $callableCreateImage = function ($image, $path) use ($filename) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
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
     * @return array<string, array{profil: array{
     *     nom: string,
     *     prenom: string,
     *     username: string,
     *     file_info: array{filename: string, mimeType: string},
     *     file: array{file: mixed}
     * }}>
     */
    public static function dataFormValidWithFileValid(): array
    {
        return [
            'data and file with extension jpeg' => [
                [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                        'username' => 'username',
                        'file_info' => [
                            'filename' => 'test.jpeg',
                            'mimeType' => 'image/jpeg',
                        ],
                        'file' => [
                            'file' => null
                        ]
                    ]
                ]
            ],
            'data and file with extension png' => [
                [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                        'username' => 'username',
                        'file_info' => [
                            'filename' => 'test.png',
                            'mimeType' => 'image/png'
                        ],
                        'file' => [
                            'file' => null
                        ]
                    ]
                ]
            ],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->temporaryFiles) {
            foreach ($this->temporaryFiles as $path) {
                unlink($path);
            }
            $this->temporaryFiles = null;
        }
        $this->client = null;
        $this->userRepository = null;
        $this->userToLogged = null;
        $this->pathUploadedFile = null;
    }
}
