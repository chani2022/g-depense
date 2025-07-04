<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

final class UserCrudControllerTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;

    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    public function testIndexPageUser(): void
    {
        // this examples doesn't use security; in your application you may
        // need to ensure that the user is logged before the test
        $this->client->request("GET", $this->generateIndexUrl());
        static::assertResponseIsSuccessful();
    }
}
