<?php

namespace App\Tests\Controller\Admin\Crud\Quantity;

use App\Controller\Admin\CapitalCrudController;
use App\Controller\Admin\DashboardController;
use App\Controller\Admin\QuantityCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractQuantityCrudTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    protected ?Crawler $crawler;

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->crawler = null;
    }

    protected function getControllerFqcn(): string
    {
        return QuantityCrudController::class;
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

    protected function logOtherUser(): void
    {
        $this->client->loginUser($this->getSimpeOtherUserAuthenticated());
    }
}
