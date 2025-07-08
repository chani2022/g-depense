<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use App\Tests\Trait\LoadFixtureTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\User;

final class UserCrudControllerTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use LoadFixtureTrait;

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
        /** @var User */
        $authenticatedUser = $this->getFixtures()['user_admin'];
        // this examples doesn't use security; in your application you may
        // need to ensure that the user is logged before the test
        $this->client->loginUser($authenticatedUser);
        $this->client->request("GET",  $this->generateIndexUrl());

        static::assertResponseIsSuccessful();
    }
    /**
     * @dataProvider userAccessDenied
     */
    public function testAccessDeniedPageIndexIfUserAnonymousAndSimpleUser(string $roles): void
    {
        if ($roles == 'roleUser') {
            $authenticatedUser = $this->getFixtures()['user_credentials_ok'];
            $this->client->loginUser($authenticatedUser);
        }

        $this->client->request("GET",  $this->generateIndexUrl());
        if ($roles == 'anonymous') {
            static::assertResponseStatusCodeSame(302);
        } else {
            static::assertResponseStatusCodeSame(403);
        }
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
