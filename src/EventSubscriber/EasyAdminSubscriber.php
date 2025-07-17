<?php

namespace App\EventSubscriber;

use App\Entity\CompteSalaire;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage) {}
    public function BeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (!$object instanceof CompteSalaire) return;

        $object->setOwner(
            $this->tokenStorage->getToken()->getUser()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'BeforeEntityPersistedEvent',
        ];
    }
}
