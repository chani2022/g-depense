<?php

namespace App\EventSubscriber;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Entity\Depense;
use App\Entity\Unite;
use App\Repository\CompteSalaireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private CompteSalaireRepository $compteSalaireRepository,
    ) {}

    public function setOwnerForEntityCompteSalaireCategoryUnite(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (
            !$object instanceof CompteSalaire &&
            !$object instanceof Category &&
            !$object instanceof Unite
        ) return;

        $object->setOwner(
            $this->tokenStorage->getToken()->getUser()
        );
    }

    public function setCompteSalaireForCapitalAndDepense(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (
            !$object instanceof Capital &&
            !$object instanceof Depense
        ) return;

        $compteSalaire = $this->compteSalaireRepository->getCompteSalaireWithDateNow($this->tokenStorage->getToken()->getUser());
        if ($compteSalaire) {
            $object->setCompteSalaire($compteSalaire);
        }
    }

    // public function setOwnerForCategory(BeforeEntityPersistedEvent $event): void
    // {
    //     $object = $event->getEntityInstance();

    //     if (!$object instanceof Category) return;

    //     $object->setOwner($this->tokenStorage->getToken()->getUser());
    // }

    // public function setOwnerForEntityUnite(BeforeEntityPersistedEvent $event): void
    // {
    //     $object = $event->getEntityInstance();

    //     if (!$object instanceof Unite) return;

    //     $object->setOwner($this->tokenStorage->getToken()->getUser());
    // }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ['setOwnerForEntityCompteSalaireCategoryUnite'],
                ['setCompteSalaireForCapitalAndDepense'],
            ]
        ];
    }
}
