<?php

namespace App\Validator;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\UniqueEntityByUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UniqueEntityByUserValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $em,
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

        $field = $constraint->field;
        $entityClass = $constraint->entityClass;

        $getter = 'get' . ucfirst($field);
        if (!method_exists($object, $getter)) {
            throw new \LogicException("La méthode $getter n'existe pas dans " . get_class($object));
        }

        if ($this->em->getCategoryByUser($user, $object)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $object)
                ->addViolation();
        }
    }
}
