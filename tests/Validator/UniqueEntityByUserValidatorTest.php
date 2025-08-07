<?php

namespace App\Tests\Validator;

use App\Entity\Category;
use App\Entity\Quantity;
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


class UniqueCategoryValidatorTest extends TestCase
{
    // === Properties ===
    private ?UniqueEntityByUserValidator $uniqueEntityByUserValidator;
    /** @var MockObject&ExecutionContextInterface&null */
    private $context;
    /** @var MockObject&EntityManagerInterface&null */
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
        $constraint = $this->simulateConstraint(field: 'notExist', mappingOwner: 'test', entityClass: 'notExist');

        $this->uniqueEntityByUserValidator->validate(null, $constraint);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider providePropsObjectExist
     */
    public function testStopIfUserAuthenticatedNotHaveId(string $object, string $mappingOwner, string $field): void
    {
        $userAuthenticated = $this->mockUserWithoutId();
        $this->simulateUserAuthenticated($userAuthenticated);
        $constraint = $this->simulateConstraint($field, $mappingOwner, $object);
        $objectToValidate = $this->mockObjectToValidate($object);

        $entityRepository = $this->createMock(EntityRepository::class);

        $this->simulateNeverCallGetRepository($entityRepository);

        $this->uniqueEntityByUserValidator->validate($objectToValidate, $constraint);
    }

    /**
     * @dataProvider providePropsObjectNotExist
     */
    public function testGetterObjectNotExist(string $object, string $mappingOwner, string $field): void
    {
        $userAuthenticated = $this->mockUserWithId();
        $this->simulateUserAuthenticated($userAuthenticated);
        $constraint = $this->simulateConstraint($field, $mappingOwner, $object);
        $object = $this->mockObjectToValidate($object);

        $this->expectException(LogicException::class);
        $this->uniqueEntityByUserValidator->validate($object, $constraint);
    }
    /**
     * @dataProvider providePropsObjectExist
     */
    public function testValueForPropsEntityAlreadyExist(string $object, string $mappingOwner, string $field): void
    {
        $constraint = $this->simulateConstraint(field: $field, mappingOwner: $mappingOwner, entityClass: $object);

        $user = $this->mockUserWithId();
        $this->simulateUserAuthenticated($user);
        $objectToValidate = $this->mockObjectToValidate($object);

        $entityRepository = $this->createMock(EntityRepository::class);
        $this->simulateGetRepository($entityRepository, $object);
        $value = $this->getValueField($objectToValidate, $field);
        $critere = $this->simulateCritereFindOnyBy($user, $constraint, $value);

        $this->simulateEntityExist($entityRepository, $critere, $objectToValidate);

        $constraintViolationBuilder = $this->mockConstraintViolationBuilder();

        $this->simulateBuildViolation(1, $constraint->message, $constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ value }}', $value)
            ->willReturnSelf();

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation')
            ->willReturnSelf();


        $this->uniqueEntityByUserValidator->validate($objectToValidate, $constraint);
    }

    /**
     * @dataProvider providePropsObjectExist
     */
    public function testValuePropsEntityNotExist(string $object, string $mappingOwner, string $field): void
    {
        $constraint = $this->simulateConstraint(field: $field, mappingOwner: $mappingOwner, entityClass: $object);

        $user = $this->mockUserWithId();
        $this->simulateUserAuthenticated($user);
        $objectToValidate = $this->mockObjectToValidate($object);

        $entityRepository = $this->createMock(EntityRepository::class);

        $this->simulateGetRepository($entityRepository, $constraint->entityClass);

        $value = $this->getValueField($objectToValidate, $field);
        $critere = $this->simulateCritereFindOnyBy($user, $constraint, $value);

        $this->simulateEntityExist($entityRepository, $critere);

        $constraintViolationBuilder = $this->mockConstraintViolationBuilder();

        $this->simulateBuildViolation(0, $constraint->message, $constraintViolationBuilder);

        $this->uniqueEntityByUserValidator->validate($objectToValidate, $constraint);
    }

    // === Data providers ===
    public static function providePropsObjectNotExist(): array
    {
        return [
            [
                'entityClass' => Category::class,
                'mappingOwner' => 'owner',
                'field' => 'NotExist'
            ],
            [
                'entityClass' => Quantity::class,
                'mappingOwner' => 'owner',
                'field' => 'NotExist'
            ]
        ];
    }

    public static function providePropsObjectExist(): array
    {
        return [
            [
                'entityClass' => Category::class,
                'mappingOwner' => 'owner',
                'field' => 'nom',
            ],
            [
                'entityClass' => Quantity::class,
                'mappingOwner' => 'owner',
                'field' => 'unite',
            ]
        ];
    }

    // === Private helper methods ===

    private function mockUserWithId(): User
    {
        return (new User())->setId(1);
    }

    private function mockUserWithoutId(): User
    {
        return new User();
    }

    private function mockObjectToValidate(string $object): object
    {
        return match ($object) {
            'App\Entity\Category' => (new Category())->setNom('test'),
            'App\Entity\Quantity' => (new Quantity())->setUnite('test')
        };
    }

    private function simulateUserAuthenticated(User $user): User
    {
        $this->token->setToken(
            new UsernamePasswordToken($user, 'main')
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

    private function simulateNeverCallGetRepository(MockObject $entityRepository): void
    {
        $this->entityManager
            ->expects($this->never())
            ->method('getRepository');
    }

    private function simulateCritereFindOnyBy(User $user, UniqueEntityByUser $constraint, string $valueField): array
    {
        return [
            $constraint->mappingOwner => $user,
            $constraint->field => $valueField
        ];
    }

    private function getValueField(object $objectToValidate, string $field): string
    {
        $getter = 'get' . ucfirst($field);

        return $objectToValidate->$getter();
    }

    private function simulateEntityExist(
        MockObject $entityRepository,
        array $critere,
        object|null $objectWillReturn = null
    ): void {
        $entityRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($critere)
            ->willReturn($objectWillReturn);
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

    private function mockConstraintViolationBuilder(): MockObject
    {
        $this->uniqueEntityByUserValidator->initialize($this->context);
        return $this->createMock(ConstraintViolationBuilderInterface::class);
    }

    private function simulateConstraint(
        string $field,
        string $mappingOwner,
        string $entityClass
    ): UniqueEntityByUser {
        $constraint = new UniqueEntityByUser(field: $field, mappingOwner: $mappingOwner, entityClass: $entityClass);
        $constraint->message = 'test';

        return $constraint;
    }
}
