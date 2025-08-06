<?php

namespace App\Tests\Validator;

use App\Entity\Category;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\CategoryRepository;
use App\Validator\UniqueEntityByUser;
use App\Validator\UniqueEntityByUserValidator;
use LogicException;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;

class UniqueCategoryValidatorTest extends TestCase
{
    // === Properties ===
    private ?UniqueEntityByUserValidator $uniqueEntityByUserValidator;
    /** @var MockObject&ExecutionContextInterface&null */
    private $context;
    /** @var MockObject&CategoryRepository&null */
    private  $categoryRepository;

    private ?TokenStorageInterface $token;

    // === Setup / Teardown ===
    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->token = new TokenStorage();
        $this->token->setToken(
            new UsernamePasswordToken(new User(), 'main')
        );
        $this->uniqueEntityByUserValidator = new UniqueEntityByUserValidator($this->categoryRepository, $this->token);
        // $this->constraint->message = 'test';
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->uniqueEntityByUserValidator = null;
        $this->context = null;
        $this->categoryRepository = null;
        $this->token = null;
    }

    // === Test cases ===

    public function testObjectToValidateNull(): void
    {
        $constraint = $this->simulateUniqueEntityByUserWithFieldAndEntityClass(field: 'notExist', entityClass: 'notExist');

        $this->uniqueEntityByUserValidator->validate(null, $constraint);

        $this->assertTrue(true);
    }

    public function testStopForUserNotAuthenticated(): void
    {
        $constraint = $this->simulateUniqueEntityByUserWithFieldAndEntityClass(field: 'test', entityClass: 'test');

        $this->uniqueEntityByUserValidator->validate(new Category(), $constraint);

        $this->assertTrue(true);
    }
    /**
     * @dataProvider providePropsObjectNotExist
     */
    public function testGetterObjectNotExist(string $object, string $props): void
    {
        $this->simulateUniqueEntityByUserWithFieldAndEntityClass(field: $props, entityClass: $object);

        $object = match ($object) {
            'category' => new Category()
        };

        $this->expectException(LogicException::class);
        $this->uniqueEntityByUserValidator->validate($object, $this->constraint);
    }

    public function testGetterObjectValid(): void {}

    public function testCategoryEntityAlreadyExist(): void
    {
        $value = 'alreadyExist';
        $user = $this->simulateUserAuthenticated();
        $this->simulateAlreadyCategoryExist($user, $value);

        $this->initializeValidatorContext();

        $constraintBuilder = $this->initConstraintBuilder();

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

        $this->uniqueEntityByUserValidator->validate($value, $this->constraint);
    }

    public function testCategoryNotExist(): void
    {
        $value = 'newCategory';
        $user = $this->simulateUserAuthenticated();

        $this->simulateCategoryNotExist($user, $value);

        $this->initializeValidatorContext();

        $constraintBuilder = $this->initConstraintBuilder();

        $this->context
            ->expects($this->never())
            ->method('buildViolation');

        $this->uniqueEntityByUserValidator->validate($value, $this->constraint);
    }

    // === Data providers ===

    public static function providePropsObjectNotExist(): array
    {
        return [
            ['category', 'NotExist']
        ];
    }

    private function initializeValidatorContext(): void
    {
        $this->uniqueEntityByUserValidator->initialize($this->context);
    }

    // === Private helper methods ===

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

    /**
     * @return array<string, {string, mixed}>
     */
    // private function simulateObject(): array
    // {
    //     $field = $this->constraint->field;
    //     $object = $this->constraint->entityClass;

    //     $getter = 'get' . ucfirst($field);

    //     return [
    //         'object' => $object,
    //         'getter' => $getter
    //     ];
    // }

    private function simulateUniqueEntityByUserWithFieldAndEntityClass(string $field, string $entityClass): UniqueEntityByUser
    {
        $constraint = new UniqueEntityByUser(field: $field, entityClass: $entityClass);
        $constraint->message = 'test';

        return $constraint;
    }
}
