<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;

class IndexCategoryCrudTest extends AbstractCategoryCrudTest
{
    use CrudTestIndexAsserts;

    public function testPageIndexCategoryAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    public function testCountEntityCategory(): void {}
}
