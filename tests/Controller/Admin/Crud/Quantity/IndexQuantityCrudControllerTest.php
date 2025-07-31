<?php

namespace App\Tests\Controller\Admin\Crud\Quantity;

use App\Tests\Controller\Admin\Crud\Capital\AbstractQuantityCrudTest;

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


    private function simulateUserAccessIndexQuantityPage(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessIndexQuantityPage(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $this->client->loginUser($userAuthenticated);

        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }
}
