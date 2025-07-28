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
        $value = $this->simulateGetCompteSalaireByDate(new Category());

        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        //demarrage du context
        $this->uniqueCategoryValidator->initialize($this->context);

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

    public function testValueNotInRangeCompteSalaireDateDebutAndDateFin(): void
    {
        $value = $this->simulateGetCompteSalaireByDate();

        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        //demarrage du context
        $this->dateBeetweenValidator->initialize($this->context);

        $this->context
            ->expects($this->never())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($constraintBuilder);

        $this->dateBeetweenValidator->validate($value, $this->constraint);
    }

    private function simulateGetCompteSalaireByDate(?Category $category = null): string
    {
        $value = 'alreadyExist';
        $this->token->setToken(
            new UsernamePasswordToken(new User, 'main')
        );
        $user = $this->token->getToken()->getUser();
        $invocation = $this->categoryRepository->expects($this->once())
            ->method('getCategoryByUser')
            ->with($user, $value);

        if ($category) {
            $invocation->willReturn($category);
        } else {
            $invocation->willReturn(null);
        }

        return $value;
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
