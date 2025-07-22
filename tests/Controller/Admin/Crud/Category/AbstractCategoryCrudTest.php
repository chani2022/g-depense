<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractCategoryCrudTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    protected ?Crawler $crawler;

    protected function getControllerFqcn(): string
    {
        return CategoryCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function logUser(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());
    }

    protected function logAdmin(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->crawler = null;
    }
}
