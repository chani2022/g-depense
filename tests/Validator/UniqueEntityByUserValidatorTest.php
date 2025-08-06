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
    private ?UniqueEntityByUser $constraint;
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
        $this->constraint = null;
        // $this->constraint->message = 'test';
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->constraint = null;
        $this->uniqueEntityByUserValidator = null;
        $this->context = null;
        $this->categoryRepository = null;
        $this->token = null;
    }

    // === Test cases ===

    public function testObjectToValidateNull(): void
    {
        $this->simulateUniqueEntityByUserWithFieldAndEntityClassInvalid();
        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->uniqueEntityByUserValidator->validate(null, $this->constraint);
    }

    public function testGetterObjectNotExist(): void
    {
        $this->expectException(LogicException::class);

        $object = new User();
        $this->simulateUniqueEntityByUserWithFieldAndEntityClassInvalid();
        $this->uniqueEntityByUserValidator->validate($object, $this->constraint);
    }

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

    // public static function provideInvalid(): array
    // {
    //     return [
    //         [''],
    //         [null]
    //     ];
    // }

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
    private function simulateGetterObject(): array
    {
        $field = $this->constraint->field;
        $object = $this->constraint->entityClass;

        $getter = 'get' . ucfirst($field);

        return [
            'object' => $object,
            'getter' => $getter
        ];
    }

    private function simulateUniqueEntityByUserWithFieldAndEntityClassInvalid(): void
    {
        $this->constraint = new UniqueEntityByUser(field: 'test', entityClass: 'test');
        $this->constraint->message = 'test';
    }
}
