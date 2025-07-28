<?php

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Tests\Trait\UserAuthenticatedTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->categoryRepository = $this->getContainer()->get(CategoryRepository::class);
    }

    public function testGetCategoryReturnCategory(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $categoryActual = $this->categoryRepository->getCategoryByUser($userAuthenticated, 'alreadyExist');

        $this->assertNotNull($categoryActual);
        $this->assertInstanceOf(Category::class, $categoryActual);
    }

    public function testGetCategoryReturnNull(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $expectActual = $this->categoryRepository->getCategoryByUser($userAuthenticated, 'NoExist');

        $this->assertNull($expectActual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->categoryRepository = null;
    }
}
