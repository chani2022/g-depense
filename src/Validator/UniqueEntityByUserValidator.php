<?php

namespace App\Validator;

use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\UniqueEntityByUser;

class UniqueEntityByUserValidator extends ConstraintValidator
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TokenStorageInterface $token
    ) {}
    /**
     * @param string $value
     * @param UniqueEntityByUser $constraint
     */
    public function validate($value, Constraint $constraint)
    {

        if (null === $value || '' === $value) {
            return;
        }

        if ($this->categoryRepository->getCategoryByUser($this->token->getToken()->getUser(), $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
