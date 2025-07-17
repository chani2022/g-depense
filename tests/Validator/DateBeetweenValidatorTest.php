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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

    private ?TokenStorageInterface $token;

    protected function setUp(): void
    {
        $this->compteSalaireRepository = $this->createMock(CompteSalaireRepository::class);
        $this->token = new TokenStorage();
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
        $value = $this->simulateGetCompteSalaireByDate(new CompteSalaire());

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

    private function simulateGetCompteSalaireByDate(?CompteSalaire $compteSalaire = null, ?DateTime $value = null): DateTime
    {
        $value = new DateTime('2025-01-01');
        $this->token->setToken(
            new UsernamePasswordToken(new User, 'main')
        );
        $user = $this->token->getToken()->getUser();
        $invocation = $this->compteSalaireRepository->expects($this->once())
            ->method('getCompteSalaireByDate')
            ->with($user, $value);

        if ($compteSalaire) {
            $invocation->willReturn($compteSalaire);
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
        $this->dateBeetweenValidator = null;
        $this->context = null;
        $this->compteSalaireRepository = null;
        $this->token = null;
    }
}
