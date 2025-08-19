<?php

namespace App\Tests\Controller\Admin\Crud\Depense;

use App\Tests\Controller\Admin\Crud\Depense\AbstractDepenseCrudTest;

class IndexDepenseControllerTest extends AbstractDepenseCrudTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testAccessDeniedPageIndexDepenseIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(302);
    }
    //-------------utilisateur simple------------------------------------

    public function testIndexPageDepenseAccessUserSuccessfully(): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();
    }

    /**
     * @dataProvider fieldShowingUserAuthenticated
     */
    public function testFieldShowingInIndexPageDepenseIfUserAuthenticated(string $field): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnExists($field);
    }
    /**
     * @dataProvider fieldNotShowingUserAuthenticated
     */
    public function testFieldNotShowingInIndexPageDepenseIfUserAuthenticated(string $field): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnNotExists($field);
    }

    public function testShowOnlyDepensesOwnerIfUserAuthenticated(): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();
        $this->assertIndexPageEntityCount(2);
    }

    //------------------admin-----------------------------------

    public function testIndexPageDepenseAccessAdminSuccessfully(): void
    {
        $this->simulateAdminAccessPageIndexDepenseSuccessfully();
    }

    /**
     * @dataProvider fieldShowingAdminAuthenticated
     */
    public function testFieldShowingInIndexPageDepenseIfAdminAuthenticated(string $field): void
    {
        $this->simulateAdminAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnExists($field);
    }

    public function testShowAllDepenseIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessPageIndexDepenseSuccessfully();
        $this->assertIndexPageEntityCount(3);
    }

    private function simulateUserAccessPageIndexDepenseSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessPageIndexDepenseSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    public static function fieldShowingUserAuthenticated(): array
    {
        return [
            ['nomDepense'],
            ['prix'],
            ['quantite'],
            ['compteSalaire.dateDebutCompte'],
            ['compteSalaire.dateFinCompte'],
            ['category.nom'],
            ['unite.unite'],
        ];
    }

    public static function fieldNotShowingUserAuthenticated(): array
    {
        return [
            ['category'],
            ['unite'],
            ['compteSalaire.owner.imageName']
        ];
    }

    public static function fieldShowingAdminAuthenticated(): array
    {
        return [
            ['compteSalaire.owner.imageName'],
            ['compteSalaire.dateDebutCompte'],
            ['compteSalaire.dateFinCompte'],
            ['nomDepense'],
            ['prix'],
            ['vital'],
            ['quantity.unite'],
            ['quantity.quantite']
        ];
    }
}
