<?php

namespace App\Tests\Controller\Admin\Crud\Unite;

use App\Tests\Controller\Admin\Crud\Unite\AbstractUniteCrudTest;

class IndexUniteCrudControllerTest extends AbstractUniteCrudTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * ---------------------security de la page ----------------------------------
     */
    public function testAccessDeniedIndexPageUniteForUserAnonymous(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    public function testUserAuthorizedForPageIndexUnite(): void
    {
        $this->simulateUserAccessIndexUnitePage();
    }

    public function testAdminAuthorizedForPageIndexUnite(): void
    {
        $this->simulateAdminAccessIndexUnitePage();
    }
    /**
     * -----------------------nombre d'entity affiché par utilisateur-------------
     */
    public function testShowOnlyOwnerUniteIfUserAuthenticated(): void
    {
        $this->simulateUserAccessIndexUnitePage();

        $this->assertIndexPageEntityCount(1);
    }

    public function testShowAllUniteIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessIndexUnitePage();

        $this->assertIndexPageEntityCount(2);
    }

    /**
     * @dataProvider fieldShowIfUserAuthenticated
     */
    public function testFieldsShowingIfUserAuthenticated($field): void
    {
        $this->simulateUserAccessIndexUnitePage();

        $this->assertIndexColumnExists($field);
    }
    /**
     * @dataProvider fieldsShowIfUserAdmin
     */
    public function testFieldsShowingIfAdminAuthenticated(string $field): void
    {
        $this->simulateAdminAccessIndexUnitePage();

        $this->assertIndexColumnExists($field);
    }

    public static function fieldShowIfUserAuthenticated(): array
    {
        return [
            ['quantite'],
            ['unite'],
        ];
    }

    public static function fieldsShowIfUserAdmin(): array
    {
        return [
            ['quantite'],
            ['unite'],
            ['owner']
        ];
    }

    /**
     * --------------------------simulation----------------------------------------
     */
    private function simulateUserAccessIndexUnitePage(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessIndexUnitePage(): void
    {
        $userAuthenticated = $this->getAdminAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }
}
