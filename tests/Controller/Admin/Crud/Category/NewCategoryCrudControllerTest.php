<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Tests\Controller\Admin\Crud\Category\AbstractCategoryCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewCategoryControllerCrudTest extends AbstractCategoryCrudTest
{
    use CrudTestFormAsserts;

    public function testPageNewCategoryAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageIndexCategorySuccessfullyIfUserAuthenticated(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();
    }

    /**
     * -----------------------------------------------------------------
     * --------------------------------utilisateur simple---------------
     * -----------------------------------------------------------------
     * @dataProvider fieldShowing
     */
    public function testPageIndexCategoryFieldShowing(string $field): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexColumnExists($field);
    }

    public function testShowOnlyOwnerEntityCategory(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexPageEntityCount(1);
    }

    /**
     * -------------------------------------------------------------
     * ----------------------------Admin----------------------------
     * -------------------------------------------------------------
     */
    public function testPageIndexCategorySuccessfullyIfAdminAuthenticated(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithAdmin();
    }

    public function testShowAllEntityCategoryIfAdmin(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithAdmin();

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
