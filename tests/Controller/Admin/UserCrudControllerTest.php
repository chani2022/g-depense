<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;

final class UserCrudControllerTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    public function testIndexPageUserAuthorized(): void
    {
        $this->simulateAccessPageIndexSuccessfully();
    }
    /**
     * @dataProvider userAccessDenied
     */
    public function testAccessDeniedPageIndexUserIfUserAnonymousAndSimpleUser(string $roles): void
    {
        if ($roles == 'roleUser') {
            $authenticatedUser = $this->getSimpeUserAuthenticated();
            $this->client->loginUser($authenticatedUser);
        }

        $this->client->request("GET",  $this->generateIndexUrl());
        if ($roles == 'anonymous') {
            static::assertResponseStatusCodeSame(302);
        } else {
            static::assertResponseStatusCodeSame(403);
        }
    }

    public function testUserAuthenticatedNotShowInPageIndexUser(): void
    {
        $this->simulateAccessPageIndexSuccessfully();
        $this->assertIndexPageEntityCount(1);
    }

    private function simulateAccessPageIndexSuccessfully(): void
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
