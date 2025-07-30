<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\HandleImage\HandleImage;
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

        $handleImage = new HandleImage();
        $resizedImage = $handleImage->open($file->getRealPath())
            ->resize(50, 50);

        $resizedImage->save($file->getRealPath(), [
            'quality' => 85,
            'format' => $file->getClientOriginalExtension()
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_UPLOAD => 'onThumbnailImage'
        ];
    }
}
