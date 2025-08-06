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

        $getter = $this->getGetter($object, $constraint);

        $resultat = $this->findOneBy($object::class, $user);

        if ($resultat) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $object->$getter())
                ->addViolation();
        }
    }

    protected function getGetter(mixed $object, Constraint $constraint)
    {
        $field = $constraint->field;

        $getter = 'get' . ucfirst($field);
        if (!method_exists($object, $getter)) {
            throw new \LogicException("La méthode $getter n'existe pas dans " . get_class($object));
        }

        return $getter;
    }

    protected function findOneBy(mixed $classname, User $user): mixed
    {
        $entityOrNull = $this->em->getRepository($classname)
            ->findOneBy([
                'owner' => $user
            ]);

        return $entityOrNull;
    }
}
