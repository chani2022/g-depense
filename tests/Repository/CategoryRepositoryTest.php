<?php

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Entity\User;
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->categoryRepository = null;
    }

    public function testGetCategoryReturnCategory(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $expectActual = $this->simulateGetCategoryByUser($userAuthenticated, 'alreadyExist');

        $this->assertNotNull($expectActual);
        $this->assertInstanceOf(Category::class, $expectActual);
    }

    public function testGetCategoryReturnNull(): void
    {
        $userAuthenticated = $this->getSimpeUserAuthenticated();
        $expectActual = $this->simulateGetCategoryByUser($userAuthenticated, 'notExist');

        $this->assertNull($expectActual);
    }

    private function simulateGetCategoryByUser(User $userAuthenticated, string $nomCategory)
    {
        return $this->categoryRepository->getCategoryByUser($userAuthenticated, $nomCategory);
    }
}
