<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;

class IndexCategoryCrudTest extends AbstractCategoryCrudTest
{
    use CrudTestIndexAsserts;

    public function testPageIndexCategoryAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageIndexCategorySuccessfullyIfUserAuthenticated(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();
    }

    public function testPageIndexCategorySuccessfullyIfAdminAuthenticated(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithAdmin();
    }

    public function testCountOwnerEntityCategory(): void
    {
        $this->logUser();

        $this->assertIndexPageEntityCount(2);
    }

    private function simulateAccessPageIndexCategorySuccessfullyWithUser(): void
    {
        $this->logUser();
        $this->crawler = $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageIndexCategorySuccessfullyWithAdmin(): void
    {
        $this->logAdmin();
        $this->crawler = $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseIsSuccessful();
    }

    public static function fieldShowing(): array
    {
        return [
            ['nom'],
            ['prix']
        ];
    }
}
