<?php

namespace App\EventSubscriber;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\Repository\CompteSalaireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function handleImageUser(BeforeEntityUpdatedEvent $event): void
    {
        $object = $event->getEntityInstance();

        if (!$object instanceof User) {
            return;
        }
        $file = $object->getFile();

        if (!$file instanceof UploadedFile) return;

        $imagine = new Imagine();
        $image = $imagine->open($file->getPathname());

        $image->resize(new Box(120, 90))
            ->save($file->getPathname(), ['quality' => 85]);
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
                ['handleImageUser']
            ]
        ];
    }
}
