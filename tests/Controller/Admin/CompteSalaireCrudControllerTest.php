<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\CompteSalaireCrudController;
use App\Controller\Admin\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use Symfony\Component\DomCrawler\Crawler;

final class CompteSalaireCrudControllerTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use CrudTestFormAsserts;
    use UserAuthenticatedTrait;

    private ?Crawler $crawler;

    protected function getControllerFqcn(): string
    {
        return CompteSalaireCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    public function testAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * ---------------------------------------------------
     * ---------------------page index-------------------------
     * ---------------------------------------------------
     */
    public function testIndexPageCompteSalaireAccessUserSuccessfully(): void
    {
        $this->simulateUserAccessPageIndexSuccessfully();
    }

    public function testIndexPageCompteSalaireAccessAdminSuccessfully(): void
    {
        $this->simulateAdminAccessPageIndexSuccessfully();
    }

    public function testShowOnlyCompteSalaireOwnerIfUserAuthenticated(): void
    {
        $this->simulateUserAccessPageIndexSuccessfully();
        $this->assertIndexPageEntityCount(3);
    }

    public function testShowAllCompteSalaireIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessPageIndexSuccessfully();
        $this->assertIndexPageEntityCount(4);
    }

    private function simulateUserAccessPageIndexSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessPageIndexSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }
    /**
     * @return array<array{string, string}>
     */
    public static function userAccessDenied(): array
    {
        return [
            ['anonymous'],
            ['roleUser']
        ];
    }

    /**
     * -------------------------------------------------------
     * ---------------------------fin page index -------------------------
     * -------------------------------------------------------
     */

    /**
     * -------------------------------------------------------
     * --------------------------page new--------------------------
     * -------------------------------------------------------
     */
    public function testPageNewCompteSalaireSuccessfullyWithAdmin(): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();
    }

    public function testPageNewCompteSalaireSuccessfullyWithUser(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();
    }
    /**
     * @dataProvider fieldsHidden
     */
    public function testNewPageFieldsHidden(string $field): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();

        $this->assertFormFieldNotExists($field);
    }
    /**
     * @dataProvider provideFormDataInvalid
     */
    public function testCreateCompteSalaireAlreadyExist(array $formData, int $expected): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $nameForm = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
            ->form([
                $nameForm => $formData
            ]);
        $this->crawler = $this->client->submit($form);
        $numberActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberActual);
    }

    public function testCreateCompteSalaireSuccessfully(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $nameForm = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
            ->form([
                $nameForm => [
                    'dateDebutCompte' => '2024-03-01',
                    'dateFinCompte' => '2024-03-15'
                ]
            ]);
        $this->crawler = $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }


    private function simulateAdminAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateUserAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }

    public static function fieldsHidden(): array
    {
        return [
            ['id'],
            ['owner']
        ];
    }

    public static function provideFormDataInvalid(): array
    {
        return [
            'date debut et fin dans une même compte' => [
                'data' => [

                    'dateDebutCompte' => '2024-01-02',
                    'dateFinCompte' => '2024-01-14'
                ],
                'expected' => 2
            ],
            'date debut dans un compte' => [
                'data' => [

                    'dateDebutCompte' => '2024-01-02',
                    'dateFinCompte' => '2024-06-14'
                ],
                'expected' => 1
            ],
            'date fin dans un compte' => [
                'data' => [

                    'dateDebutCompte' => '2024-06-02',
                    'dateFinCompte' => '2024-01-14'
                ],
                'expected' => 1
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->crawler = null;
    }
}
