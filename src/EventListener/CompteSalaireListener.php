<?php

namespace App\EventListener;

use App\Entity\CompteSalaire;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: CompteSalaire::class)]
class CompteSalaireListener
{
    public function __construct(private TokenStorageInterface $tokenStorage) {}
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function prePersist(CompteSalaire $compteSalaire, PrePersistEventArgs $event): void
    {
        $compteSalaire->setOwner(
            $this->tokenStorage->getToken()->getUser()
        );
    }
}
