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
     * @param object $object
     * @param UniqueEntityByUser $constraint
     */
    public function validate(mixed $object, Constraint $constraint)
    {

        if (null === $object) {
            return;
        }

        /** @var User */
        $user = $this->token->getToken()->getUser();

        $value = $this->getFieldValue($object, $constraint);
        $field = $constraint->field;

        $critere = [
            'owner' => $user,
            $field => $value
        ];

        $resultat = $this->findOneBy($object::class, $critere);

        if ($resultat) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    protected function getFieldValue(mixed $object, Constraint $constraint)
    {
        $field = $constraint->field;

        $getter = 'get' . ucfirst($field);
        if (!method_exists($object, $getter)) {
            throw new \LogicException("La méthode $getter n'existe pas dans " . get_class($object));
        }

        return $object->$getter();
    }

    protected function findOneBy(mixed $classname, array $critere): ?object
    {
        $entityOrNull = $this->em->getRepository($classname)
            ->findOneBy($critere);

        return $entityOrNull;
    }
}
