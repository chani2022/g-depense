<?php

namespace App\Tests\Controller;

use App\Tests\Trait\LoadFixtureTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\User;
use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ProfilControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use LoadFixtureTrait;

    private ?KernelBrowser $client;
    private ?array $pathMockFile;
    private ?Crawler $crawler;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->pathMockFile = [];
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
    /**
     * @dataProvider formDataValid
     */
    public function testSubmitFormSuccess(array $formData): void
    {
        $formData = $this->handleFormData($formData);

        $this->simulateAccesPageProfil();

        $this->simulateSubmitForm($formData);

        $this->assertUserUpdated($formData);

        $this->assertResponseStatusCodeSame(302);

        $this->client->followRedirect();
    }

    private function assertUserUpdated(array $formData): void
    {
        /** @var UserRepository */
        $userRepository = $this->getContainer()->get(UserRepository::class);
        /** @var User */
        $userExpected = $userRepository->findOneByUsername($formData['profil']['username']);

        $this->assertInstanceOf(User::class, $userExpected);
        $this->assertEquals(strtoupper($formData['profil']['nom']), $userExpected->getNom());
        $this->assertEquals(ucwords($formData['profil']['prenom']), $userExpected->getPrenom());

        if ($userExpected->getImageName()) {
            $dirFileUploaded = $this->getContainer()->getParameter('path_uploaded_image_users');
            $pathFileUploaded = $dirFileUploaded . DIRECTORY_SEPARATOR . $userExpected->getImageName();
            $this->pathMockFile[] = $pathFileUploaded;
            $this->assertFileExists($pathFileUploaded);
        }
    }

    private function simulateSubmitForm(array $formData): void
    {
        $form = $this->crawler->selectButton('Modifier')->form($formData);
        $this->client->submit($form);
    }
    /**
     * modification de donnée en ajoutant la clés file et suppression du file_info
     */
    private function handleFormData(array $formData): array
    {
        $path = null;
        if (array_key_exists('file_info', $formData['profil'])) {
            $path = $this->mockFile($formData['profil']['file_info']['mimetype'], $formData['profil']['file_info']['filename']);
            $filename = $formData['profil']['file_info']['filename'];
            $formData['profil']['file']['file'] = new UploadedFile($path, $filename);
            unset($formData['profil']['file_info']);
            $this->pathMockFile[] = $path;
        }
        return $formData;
    }

    private function simulateAccesPageProfil(): void
    {
        $authenticatedUser = $this->getFixtures()['user_credentials_ok'];
        $this->client->loginUser($authenticatedUser);
        $this->crawler = $this->client->request('GET', '/profil');
        $this->assertResponseIsSuccessful();
    }

    private function mockFile(string $mimeType, string $filename): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $image = imagecreatetruecolor(10, 10);
        if ($mimeType == 'image/png') {
            imagepng($image, $path);
        } else {
            imagejpeg($image, $path);
        }

        return $path;
    }

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
            'data with file extension .png' => [
                [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                        'username' => 'mon username',
                        'file_info' => [
                            'mimetype' => 'image/png',
                            'filename' => 'test.png'
                        ]
                    ]
                ]
            ],
            'data with file extension .jpeg' => [
                [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                        'username' => 'mon username',
                        'file_info' => [
                            'mimetype' => 'image/jpeg',
                            'filename' => 'test.jpeg'
                        ]
                    ]
                ]
            ],
            'data without file' => [
                [
                    'profil' => [
                        'nom' => 'nom',
                        'prenom' => 'prenom',
                        'username' => 'mon username',
                    ]
                ]
            ],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->pathMockFile) {
            foreach ($this->pathMockFile as $path) {
                unlink($path);
            }
        }
        $this->pathMockFile = null;
        $this->client = null;
        $this->crawler = null;
    }
}
