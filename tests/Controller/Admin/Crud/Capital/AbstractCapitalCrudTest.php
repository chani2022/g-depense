<?php

namespace App\Tests\Controller\Admin\Crud\Capital;

use App\Controller\Admin\CapitalCrudController;
use App\Controller\Admin\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Tests\Trait\UserAuthenticatedTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractCapitalCrudTest extends AbstractCrudTestCase
{
    use RefreshDatabaseTrait;
    use CrudTestFormAsserts;
    use UserAuthenticatedTrait;

    protected ?Crawler $crawler;

    protected function getControllerFqcn(): string
    {
        return CapitalCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->crawler = null;
    }
}
