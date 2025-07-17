<?php

namespace App\Tests\Validator;

use App\Repository\CompteSalaireRepository;
use App\Validator\DateBeetween;
use App\Validator\DateBeetweenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\CompteSalaire;
use App\Entity\User;
use DateTime;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DateBeetweenValidatorTest extends TestCase
{
    private ?DateBeetweenValidator $dateBeetweenValidator;
    private ?DateBeetween $constraint;
    /** @var MockObject&ExecutionContextInterface&null */
    private $context;
    /** @var MockObject&CompteSalaireRepository&null */
    private  $compteSalaireRepository;
    /** @var MockObject&UsernamePasswordToken&null */
    private $token;

    protected function setUp(): void
    {
        $this->compteSalaireRepository = $this->createMock(CompteSalaireRepository::class);
        $this->token = new UsernamePasswordToken(new User(), 'main');
        $this->dateBeetweenValidator = new DateBeetweenValidator($this->compteSalaireRepository, $this->token);
        $this->constraint = new DateBeetween('strict');
        $this->constraint->message = 'test';
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }
    /**
     * @dataProvider provideInvalid
     */
    public function testValueMissing(?string $value): void
    {
        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->dateBeetweenValidator->validate($value, $this->constraint);
    }

    public function testValueInRangeCompteSalaireDateDebutAndDateFinThrowError(): void
    {
        $value = new DateTime('2025-01-01');
        $user = $this->token->getUser();
        $compteSalaire = new CompteSalaire();
        $this->compteSalaireRepository->expects($this->once())
            ->method('getCompteSalaireByDate')
            ->with($user, $value)
            ->willReturn($compteSalaire);

        $constraintBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        //demarrage du context
        $this->dateBeetweenValidator->initialize($this->context);

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($constraintBuilder);

        $constraintBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ value }}', $value->format('d-m-Y'))
            ->willReturnSelf();

        $constraintBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->dateBeetweenValidator->validate($value, $this->constraint);
    }

    public function testValueNotInRangeCompteSalaireDateDebutAndDateFin(): void
    {
        $value = new DateTime('2025-01-01');
        $user = new User();
        $this->compteSalaireRepository->expects($this->once())
            ->method('getCompteSalaireByDate')
            ->with($user, $value)
            ->willReturn(null);

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
        $this->token = null;
    }
}
