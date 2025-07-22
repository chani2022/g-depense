<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;

class IndexCategoryControllerCrudTest extends AbstractCategoryCrudTest
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
    /**
     * @dataProvider fieldShowing
     */
    public function testPageIndexCategoryFieldShowing(string $field): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexColumnExists($field);
    }

    public function testCountOwnerEntityCategory(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexPageEntityCount(1);
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

    /**
     * @return array<string[]>
     */
    public static function fieldShowing(): array
    {
        return [
            ['id'],
            ['nom'],
            ['prix']
        ];
    }
}
