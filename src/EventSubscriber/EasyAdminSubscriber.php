<?php

namespace App\EventSubscriber;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Repository\CompteSalaireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private CompteSalaireRepository $compteSalaireRepository
    ) {}
    public function setOwnerForCompteSalaire(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (!$object instanceof CompteSalaire) return;

        $object->setOwner(
            $this->tokenStorage->getToken()->getUser()
        );
    }

    public function setCompteSalaireForCapital(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (!$object instanceof Capital) return;

        $compteSalaire = $this->compteSalaireRepository->getCompteSalaireWithDateNow($this->tokenStorage->getToken()->getUser());
        if ($compteSalaire) {
            $object->setCompteSalaire($compteSalaire);
        }
    }

    public function setOwnerForCategory(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (!$object instanceof Category) return;

        $object->setOwner($this->tokenStorage->getToken()->getUser());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ['setOwnerForCompteSalaire'],
                ['setCompteSalaireForCapital'],
                ['setOwnerForCategory']
            ],
            BeforeEntityUpdatedEvent::class => [
                ['handleImage']
            ]
        ];
    }
}
