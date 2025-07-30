<?php

namespace App\Validator;

use App\Repository\CompteSalaireRepository;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateBeetweenValidator extends ConstraintValidator
{
    public function __construct(private CompteSalaireRepository $compteSalaireRepository, private TokenStorageInterface $token) {}
    /**
     * @param DateTime $value
     * @param DateBeetween $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($this->compteSalaireRepository->getCompteSalaireByDate($this->token->getToken()->getUser(), $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('d-m-Y'))
                ->addViolation();
        }
    }
}
