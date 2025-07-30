<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\HandleImage\HandleImage;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class DoctrineSubscriber implements EventSubscriberInterface
{
    public function onPostUpload(Event $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof User) return;

        $mapping = $event->getMapping();
        $file = $mapping->getFile($object);

        // $pathname = $file->getPathname();
        // $handleImage = new HandleImage(new Imagine(), new Box(50, 50), ImageInterface::THUMBNAIL_INSET);
        // $handleImage->open($file->getRealPath())
        //     ->thumbnail()
        //     ->save($file->getRealPath());
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
            Events::PRE_UPLOAD => 'onPostUpload'
        ];
    }
}
