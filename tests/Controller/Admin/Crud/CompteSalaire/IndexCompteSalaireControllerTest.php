<?php

namespace App\Tests\Controller\Admin\Crud\CompteSalaire;

use App\Tests\Controller\Admin\Crud\CompteSalaire\AbstractCompteSalaireCrudTest;

class IndexCompteSalaireControllerTest extends AbstractCompteSalaireCrudTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(302);
    }

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
        $this->assertIndexPageEntityCount(4);
    }

    public function testShowAllCompteSalaireIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessPageIndexSuccessfully();
        $this->assertIndexPageEntityCount(5);
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
}
