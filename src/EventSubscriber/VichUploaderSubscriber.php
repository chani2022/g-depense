<?php

namespace App\EventSubscriber;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Repository\CompteSalaireRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class VichUploaderSubscriber implements EventSubscriberInterface
{

    public function handleImage(Event $event): void
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PRE_INJECT => [
                ['handleImage'],
            ],
        ];
    }
}
