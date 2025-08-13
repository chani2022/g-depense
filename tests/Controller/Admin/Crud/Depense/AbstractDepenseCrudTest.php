<?php

namespace App\Tests\Controller\Admin\Crud\Depense;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\DepenseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractDepenseCrudTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    protected ?Crawler $crawler;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->crawler = null;
    }

    protected function getControllerFqcn(): string
    {
        return DepenseCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
