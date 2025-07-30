<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class VichUploaderSubscriber implements EventSubscriberInterface
{
    public function onThumbnailImage(Event $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof User) return;

        $mapping = $event->getMapping();
        $file = $mapping->getFile($object);

        $imagine = new Imagine();
        $image = $imagine->open($file->getRealPath());

        // Redimensionne l'image (ex: largeur max 1200px)
        $resizedImage = $image->resize(new Box(50, 50));

        // Écrase le fichier original avec la version redimensionnée
        $resizedImage->save($file->getRealPath(), ['quality' => 85, 'format' => $file->getClientOriginalExtension()]);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_UPLOAD => 'onThumbnailImage'
        ];
    }
}
