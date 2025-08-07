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
     * @dataProvider providePropsObjectNotExist
     */
    public function testGetterObjectNotExist(string $object, string $mappingOwner, string $props): void
    {
        $constraint = $this->simulateConstraint(field: $props, mappingOwner: $mappingOwner, entityClass: $object);

        $object = match ($object) {
            'category' => new Category(),
            'quantity' => new Quantity()
        };

        $this->expectException(LogicException::class);
        $this->uniqueEntityByUserValidator->validate($object, $constraint);
    }

    public function testValueForPropsEntityAlreadyExist(): void
    {
        $object = (new Category())
            ->setNom('test');
        $user = $this->simulateUserAuthenticated();
        $constraint = $this->simulateConstraint(field: 'nom', mappingOwner: 'owner', entityClass: Category::class);

        $entityRepository = $this->createMock(EntityRepository::class);

        $this->simulateGetRepository($entityRepository, $constraint->entityClass);

        $critere = $this->simulateCritereFindOnyBy($user, $constraint, $object->getNom());

        $this->simulateFindOneBy($entityRepository, $critere, $object);

        $constraintViolationBuilder = $this->mockConstraintViolationBuilder();

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

    public function testPropsEntityNotExist(): void
    {
        $object = (new Category())
            ->setNom('test');
        $user = $this->simulateUserAuthenticated();
        $constraint = $this->simulateConstraint(field: 'nom', entityClass: Category::class);

        $entityRepository = $this->createMock(EntityRepository::class);

        $this->simulateGetRepository($entityRepository, $constraint->entityClass);

        $critere = $this->simulateCritereFindOnyBy($user, $constraint, $object->getNom());

        $this->simulateFindOneBy($entityRepository, $critere);

        $constraintViolationBuilder = $this->mockConstraintViolationBuilder();

        $this->simulateBuildViolation(0, $constraint->message, $constraintViolationBuilder);

        $this->uniqueEntityByUserValidator->validate($object, $constraint);
    }

    // === Data providers ===

    public static function providePropsObjectNotExist(): array
    {
        return [
            ['category', 'owner', 'NotExist'],
            ['quantity', 'user', 'NotExist']
        ];
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

    private function simulateCritereFindOnyBy(User $user, UniqueEntityByUser $constraint, string $valuePropsToValidate): array
    {
        return [
            'owner' => $user,
            $constraint->field => $valuePropsToValidate
        ];
    }

    private function simulateFindOneBy(
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
