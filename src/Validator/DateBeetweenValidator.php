<?php

namespace App\Validator;

use App\Repository\CompteSalaireRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateBeetweenValidator extends ConstraintValidator
{
    public function __construct(private CompteSalaireRepository $compteSalaireRepository) {}
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\DateBeetween $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if ($this->compteSalaireRepository->getCompteSalaireByDate($value)) {
            // TODO: implement the validation here
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
