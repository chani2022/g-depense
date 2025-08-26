<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Tests\Controller\Admin\Crud\Category\AbstractCategoryCrudTest;

class IndexCategoryControllerCrudTest extends AbstractCategoryCrudTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testPageIndexCategoryAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

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
     * @dataProvider fieldShowingUserAuthenticated
     */
    public function testPageIndexCategoryFieldShowingIfUserAuthenticated(string $field): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexColumnExists($field);
    }
    /**
     * @dataProvider fieldNotShowingUserAuthenticated
     */
    public function testPageIndexCategoryFieldNotShowingIfUserAuthenticated(string $field): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexColumnNotExists($field);
    }

    public function testShowOnlyOwnerEntityCategory(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithUser();

        $this->assertIndexPageEntityCount(2);
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
    /**
     * @dataProvider fieldShowingUserAdmin
     */
    public function testPageIndexCategoryAllFieldShowingIfAdminAuthenticated(string $field): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithAdmin();

        $this->assertIndexColumnExists($field);
    }

    public function testShowAllEntityCategoryIfAdmin(): void
    {
        $this->simulateAccessPageIndexCategorySuccessfullyWithAdmin();

        $this->assertIndexPageEntityCount(3);
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
    public static function fieldShowingUserAuthenticated(): array
    {
        return [
            ['id'],
            ['nom'],
        ];
    }

    public static function fieldNotShowingUserAuthenticated(): array
    {
        return [
            ['owner.imageName']
        ];
    }

    /**
     * @return array<string[]>
     */
    public static function fieldShowingUserAdmin(): array
    {
        return [
            ['id'],
            ['nom'],
            ['owner.imageName']
        ];
    }
}
