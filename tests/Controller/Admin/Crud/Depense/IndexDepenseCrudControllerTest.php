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

    public function testIndexPageDepenseAccessUserSuccessfully(): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();
    }

    public function testIndexPageDepenseAccessAdminSuccessfully(): void
    {
        $this->simulateAdminAccessPageIndexDepenseSuccessfully();
    }
    /**
     * @dataProvider fieldShowingUserAuthenticated
     */
    public function testFieldShowingInIndexPageDepenseIfUserAuthenticated(string $field): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnExists($field);
    }

    public function testShowOnlyDepensesOwnerIfUserAuthenticated(): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();
        $this->assertIndexPageEntityCount(2);
    }

    public function testShowAllDepenseIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessPageIndexDepenseSuccessfully();
        $this->assertIndexPageEntityCount(3);
    }

    private function simulateUserAccessPageIndexDepenseSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessPageIndexDepenseSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider fieldsHidden
     */
    public function testIndexPageDepenseFieldsHidden(string $field): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnNotExists($field);
    }

    /**
     * @dataProvider fieldsShowing
     */
    public function testIndexPageDepenseFieldsShowing(string $field): void
    {
        $this->simulateUserAccessPageIndexDepenseSuccessfully();

        $this->assertIndexColumnExists($field);
    }

    public static function fieldsHidden(): array
    {
        return [
            ['id'],
        ];
    }

    public static function fieldShowingUserAuthenticated(): array
    {
        return [
            ['compteSalaire.dateDebutCompte'],
            ['compteSalaire.dateFinCompte'],
            ['nom'],
            ['prix'],
            ['isVital'],
            ['quantity.unite'],
            ['quantity.quantite']
        ];
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
}
