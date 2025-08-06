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
use App\Validator\UniqueEntityByUser;
use App\Validator\UniqueEntityByUserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LogicException;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;

class UniqueCategoryValidatorTest extends TestCase
{
    // === Properties ===
    private ?UniqueEntityByUserValidator $uniqueEntityByUserValidator;
    /** @var MockObject&ExecutionContextInterface&null */
    private $context;
    /** @var MockObject&CategoryRepository&null */
    private  $entityManager;

    private ?TokenStorageInterface $token;

    // === Setup / Teardown ===
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->token = new TokenStorage();
        $this->token->setToken(
            new UsernamePasswordToken(new User(), 'main')
        );
        $this->uniqueEntityByUserValidator = new UniqueEntityByUserValidator($this->entityManager, $this->token);
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->uniqueEntityByUserValidator = null;
        $this->context = null;
        $this->entityManager = null;
        $this->token = null;
    }

    // === Test cases ===

    public function testObjectToValidateNull(): void
    {
        $constraint = $this->simulateConstraint(field: 'notExist', entityClass: 'notExist');

        $this->uniqueEntityByUserValidator->validate(null, $constraint);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider providePropsObjectNotExist
     */
    public function testGetterObjectNotExist(string $object, string $props): void
    {
        $constraint = $this->simulateConstraint(field: $props, entityClass: $object);

        $object = match ($object) {
            'category' => new Category()
        };

        $this->expectException(LogicException::class);
        $this->uniqueEntityByUserValidator->validate($object, $constraint);
    }

    public function testPropsEntityAlreadyExist(): void
    {
        $object = (new Category())
            ->setNom('test');
        $user = $this->simulateUserAuthenticated();
        $constraint = $this->simulateConstraint(field: 'nom', entityClass: Category::class);

        $entityRepository = $this->createMock(EntityRepository::class);

        $this->simulateGetRepository($entityRepository, $constraint->entityClass);

        $this->simulateFindOneBy($entityRepository, $user, $object);

        $this->uniqueEntityByUserValidator->initialize($this->context);
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->simulateBuildViolation(1, $constraint->message, $constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ value }}', $object->getNom())
            ->willReturnSelf();

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation')
            ->willReturnSelf();


        $this->uniqueEntityByUserValidator->validate($object, $constraint);
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
            new UsernamePasswordToken((new User())->setId(1), 'main')
        );

        return $this->token->getToken()->getUser();
    }

    private function simulateGetRepository(MockObject $entityRepository, string $argument): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($argument)
            ->willReturn($entityRepository);
    }

    private function simulateFindOneBy(MockObject $entityRepository, User $user, object|null $object = null): void
    {
        $entityRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['owner' => $user])
            ->willReturn($object);
    }

    private function simulateBuildViolation(
        int $nombreExpects,
        string $argument,
        MockObject $constraintViolationBuilder
    ): void {
        $this->context
            ->expects($nombreExpects == 0 ? $this->never() : $this->exactly($nombreExpects))
            ->method('buildViolation')
            ->with($argument)
            ->willReturn($constraintViolationBuilder);
    }

    private function simulateUserNotAuthenticated(): User
    {
        $this->token->setToken(
            new UsernamePasswordToken(new User, 'main')
        );

        return $this->token->getToken()->getUser();
    }

    private function simulatePropsAlreadyExist(User $user, string $value)
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

    private function simulateConstraint(string $field, string $entityClass): UniqueEntityByUser
    {
        $constraint = new UniqueEntityByUser(field: $field, entityClass: $entityClass);
        $constraint->message = 'test';

        return $constraint;
    }
}
