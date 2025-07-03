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
    private ?Crawler $crawler;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
        $this->pathUploadedFile = $this->getContainer()->getParameter('path_uploaded_image_users');
        $this->userToLogged = $this->getFixtures()['user_credentials_ok'];
        $this->temporaryFiles = [];

        $this->client->loginUser($this->userToLogged);
        $this->crawler = $this->client->request("GET", '/profil');
    }

    public function testPageProfilExist(): void
    {
        // $this->simulateAccessPageProfil();

        static::assertResponseIsSuccessful();
        $this->assertPageTitleSame('Profil');
    }
    /**
     * @dataProvider dataFormValidWithFileValid
     */
    public function testProfilSuccess(array $dataForm): void
    {
        // $crawler = $this->simulateAccessPageProfil();

        $pathMock = $this->simulateSubmitForm($dataForm);

        /** @var User */
        $user = $this->userRepository->find($this->userToLogged->getId());
        $this->temporaryFiles[] = $pathMock;
        $this->temporaryFiles[] = $this->pathFileUploaded($user);

        $this->assertEquals('NOM', $user->getNom());
        $this->assertEquals('Prenom', $user->getPrenom());
        $this->assertEquals('username', $user->getUsername());
        $this->assertFileExists($this->pathFileUploaded($user));
    }

    private function simulateSubmitForm(array $dataForm): ?string
    {
        $pathMock = null;
        /**
         * traitement du fichier à uploader
         */
        if (array_key_exists('file_info', $dataForm['profil'])) {
            $filename = $dataForm['profil']['file_info']['filename'];
            $mimeType = $dataForm['profil']['file_info']['mimeType'];
            unset($dataForm['profil']['file_info']); //on supprime car c'est unitule dans le form et ça provoque une erreur

            $pathMock = $this->mockFileValid($filename);

            $uploadedFile = new UploadedFile($pathMock, $filename, $mimeType, null, true);
            $dataForm['profil']['file']['file'] = $uploadedFile;
        }
        $form = $this->crawler->selectButton('Modifier')->form($dataForm);
        $this->crawler = $this->client->submit($form);

        return $pathMock;
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
     * @dataProvider formDataInvalidWithAndWithoutFile
     */
    public function testProfilFormDataInvalid(array $formData, int $nbExpected): void
    {
        $this->simulateSubmitForm($formData);

        $this->assertResponseIsSuccessful();
        $nbErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSelectorExists('.invalid-feedback');
        $this->assertEquals($nbExpected, $nbErrorActual);
    }

    public static function formDataInvalidWithAndWithoutFile(): array
    {
        return [
            'nom missing and no file' => [
                "data" => [
                    'profil' => [
                        'prenom' => 'prenom',
                        'username' => 'username',
                    ]
                ],
                "nbExpected" => 1
            ],
            'prenom missing and no file' => [
                'data' => [
                    'profil' => [
                        'nom' => 'nom',
                        'username' => 'username',
                    ]
                ],
                'nbExpected' => 1
            ],
            'username missing and no file' => [
                'data' => [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                    ]
                ],
                'nbExpected' => 1
            ],
            'nom, prenom, username missing and no file' => [
                'data' => [
                    'profil' => []
                ],
                'nbExpected' => 3
            ],
        ];
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
        $this->crawler = null;
    }
}
