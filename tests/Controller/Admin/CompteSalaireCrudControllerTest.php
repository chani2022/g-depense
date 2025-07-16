<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\CompteSalaireCrudController;
use App\Controller\Admin\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

final class CompteSalaireCrudControllerTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use CrudTestFormAsserts;
    use UserAuthenticatedTrait;

    protected function getControllerFqcn(): string
    {
        return CompteSalaireCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    public function testAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * ---------------------------------------------------
     * ---------------------page index-------------------------
     * ---------------------------------------------------
     */
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

    /**
     * -------------------------------------------------------
     * ---------------------------fin page index -------------------------
     * -------------------------------------------------------
     */

    /**
     * -------------------------------------------------------
     * --------------------------page new--------------------------
     * -------------------------------------------------------
     */
    public function testPageNewCompteSalaireSuccessfully(): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();
    }
    /**
     * @dataProvider fieldsHidden
     */
    public function testNewPageFieldsHidden(string $field): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();

        $this->assertFormFieldNotExists($field);
    }

    private function simulateAdminAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }



    public static function fieldsHidden(): array
    {
        return [
            ['id'],
            ['owner']
        ];
    }
}
