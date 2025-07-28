<?php

namespace App\Tests\Validator;

use App\Entity\Category;
use App\Validator\DateBeetween;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\Validator\UniqueCategory;
use App\Validator\UniqueCategoryValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Invocation;

class UniqueCategoryValidatorTest extends TestCase
{
    private ?UniqueCategoryValidator $uniqueCategoryValidator;
    private ?UniqueCategory $constraint;
    /** @var MockObject&ExecutionContextInterface&null */
    private $context;
    /** @var MockObject&CategoryRepository&null */
    private  $categoryRepository;

    private ?TokenStorageInterface $token;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->token = new TokenStorage();
        $this->uniqueCategoryValidator = new UniqueCategoryValidator($this->categoryRepository, $this->token);
        $this->constraint = new UniqueCategory();
        $this->constraint->message = 'test';
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }
    /**
     * @dataProvider provideInvalid
     */
    public function testValueMissingCategory(?string $value): void
    {
        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->uniqueCategoryValidator->validate($value, $this->constraint);
    }

    public function testCategoryAlreadyExist(): void
    {
        $value = 'alreadyExist';
        $user = $this->simulateUserAuthenticated();
        $this->simulateAlreadyCategoryExist($user, $value);

        //demarrage du context
        $this->uniqueCategoryValidator->initialize($this->context);

        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($constraintBuilder);

        $constraintBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ value }}', $value)
            ->willReturnSelf();

        $constraintBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->uniqueCategoryValidator->validate($value, $this->constraint);
    }

    public function testCategoryNotExist(): void
    {
        $value = 'newCategory';
        $user = $this->simulateUserAuthenticated();

        $this->simulateCategoryNotExist($user, $value);

        //demarrage du context
        $this->uniqueCategoryValidator->initialize($this->context);

        // $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintBuilder = $this->initConstraintBuilder();

        $this->context
            ->expects($this->never())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($constraintBuilder);

        $this->uniqueCategoryValidator->validate($value, $this->constraint);
    }

    private function simulateUserAuthenticated(): User
    {
        $this->token->setToken(
            new UsernamePasswordToken(new User, 'main')
        );

        return $this->token->getToken()->getUser();
    }

    private function simulateAlreadyCategoryExist(User $user, string $value)
    {
        $category = new Category();
        $invocation = $this->simulateExpectCategoryByUser($user, $value);
        $invocation->willReturn($category);
    }

    private function simulateCategoryNotExist(User $user, string $value)
    {
        $invocation = $this->simulateExpectCategoryByUser($user, $value);
        $invocation->willReturn(null);
    }

    private function simulateExpectCategoryByUser(User $user, $value): InvocationMocker
    {
        return $this->categoryRepository
            ->expects($this->once())
            ->method('getCategoryByUser')
            ->with($user, $value);
    }

    private function initConstraintBuilder(): MockObject
    {
        return $this->createMock(ConstraintViolationBuilderInterface::class);
    }

    public static function provideInvalid(): array
    {
        return [
            [''],
            [null]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->constraint = null;
        $this->uniqueCategoryValidator = null;
        $this->context = null;
        $this->categoryRepository = null;
        $this->token = null;
    }
}
