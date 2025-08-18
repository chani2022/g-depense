<?php

namespace App\Tests\Controller\Admin\Crud\Quantity;

use App\Tests\Controller\Admin\Crud\Quantity\AbstractQuantityCrudTest;

class IndexQuantityCrudControllerTest extends AbstractQuantityCrudTest
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
    public function testAccessDeniedIndexPageQuantityForUserAnonymous(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    public function testUserAuthorizedForPageIndexQuantity(): void
    {
        $this->simulateUserAccessIndexQuantityPage();
    }

    public function testAdminAuthorizedForPageIndexQuantity(): void
    {
        $this->simulateAdminAccessIndexQuantityPage();
    }
    /**
     * -----------------------nombre d'entity affiché par utilisateur-------------
     */
    public function testShowOnlyOwnerQuantityIfUserAuthenticated(): void
    {
        $this->simulateUserAccessIndexQuantityPage();

        $this->assertIndexPageEntityCount(1);
    }

    public function testShowAllQuantityIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessIndexQuantityPage();

        $this->assertIndexPageEntityCount(2);
    }

    /**
     * @dataProvider fieldShowIfUserAuthenticated
     */
    public function testFieldsShowingIfUserAuthenticated($field): void
    {
        $this->simulateUserAccessIndexQuantityPage();

        $this->assertIndexColumnExists($field);
    }
    /**
     * @dataProvider fieldsShowIfUserAdmin
     */
    public function testFieldsShowingIfAdminAuthenticated(string $field): void
    {
        $this->simulateAdminAccessIndexQuantityPage();

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
    private function simulateUserAccessIndexQuantityPage(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessIndexQuantityPage(): void
    {
        $userAuthenticated = $this->getAdminAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }
}
