<?php

namespace App\Validator;

use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\UniqueEntityByUser;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UniqueEntityByUserValidator extends ConstraintValidator
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TokenStorageInterface $token
    ) {}
    /**
     * @param mixed $value
     * @param UniqueEntityByUser $constraint
     */
    public function validate(mixed $object, Constraint $constraint)
    {
        if (null === $object) {
            return;
        }

        /** @var User */
        $user = $this->token->getToken()->getUser();

        if ($user && !$user->getId()) {
            return;
        }

        $field = $constraint->field;
        $entityClass = $constraint->entityClass;

        $getter = 'get' . ucfirst($field);
        if (!method_exists($object, $getter)) {
            throw new \LogicException("La méthode $getter n'existe pas dans " . get_class($object));
        }

        if ($this->categoryRepository->getCategoryByUser($this->token->getToken()->getUser(), $object)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $object)
                ->addViolation();
        }
    }
}
