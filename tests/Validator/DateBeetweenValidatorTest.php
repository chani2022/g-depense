<?php

namespace App\Tests\Validator;

use App\Repository\CompteSalaireRepository;
use App\Validator\DateBeetween;
use App\Validator\DateBeetweenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\CompteSalaire;
use DateTime;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class DateBeetweenValidatorTest extends TestCase
{
    private ?DateBeetweenValidator $dateBeetweenValidator;
    private ?DateBeetween $constraint;
    private ?MockObject $context;
    /** @var MockObject&CompteSalaireRepository&null */
    private  $compteSalaireRepository;

    protected function setUp(): void
    {
        $this->compteSalaireRepository = $this->createMock(CompteSalaireRepository::class);
        $this->dateBeetweenValidator = new DateBeetweenValidator($this->compteSalaireRepository);
        $this->constraint = new DateBeetween('strict');
        $this->constraint->message = 'test';
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }
    /**
     * @dataProvider provideInvalid
     */
    public function testValidValueNullAndContentNone(?string $value): void
    {

        $this->context->expects($this->never())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturnSelf();

        $this->dateBeetweenValidator->validate($value, $this->constraint);
    }

    public function testValidate(): void
    {
        $value = '2025-01-01';

        $compteSalaire = new CompteSalaire();
        $this->compteSalaireRepository->expects($this->once())
            ->method('getCompteSalaireByDate')
            ->with($value)
            ->willReturn($compteSalaire);

        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($constraintBuilder);

        $constraintBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{  value }}', $value)
            ->willReturnSelf();

        $constraintBuilder->expects($this->once())
            ->method('addViolation')
            ->willReturn('test');

        $this->dateBeetweenValidator->validate($value, $this->constraint);
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
        $this->dateBeetweenValidator = null;
        $this->context = null;
        $this->compteSalaireRepository = null;
    }
}
