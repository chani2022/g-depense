<?php

namespace App\Tests\Controller\Admin\Crud\Capital;

use App\Tests\Controller\Admin\Crud\CompteSalaire\AbstractCapitalCrudTest;

class IndexCapitalControllerTest extends AbstractCapitalCrudTest
{
    public function testAccessDeniedPageIndexCapitalIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(302);
    }

    public function testIndexPageCapitalAccessUserSuccessfully(): void
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
}
