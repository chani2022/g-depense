<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\VichUploaderSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class VichUploaderSubscriberTest extends TestCase
{
    private ?VichUploaderSubscriber $vichUploaderSubscriber;

    protected function setUp(): void
    {
        $this->vichUploaderSubscriber = new VichUploaderSubscriber();
    }

    protected function tearDown(): void
    {
        $this->vichUploaderSubscriber = null;
    }

    public function testGetSubscribedEventsVichUploader(): void
    {
        $subscribedEvents = $this->vichUploaderSubscriber->getSubscribedEvents();
        $this->assertArrayHasKey(Events::class, $subscribedEvents);
    }

    public function testHandleImage(): void
    {
        $user = new User();
        $propertyMapping = new PropertyMapping($user->getFile(), $user->getImageName());
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->vichUploaderSubscriber);
        $event = new Event($user, $propertyMapping);
        $eventDispatcher->dispatch($event, Events::PRE_INJECT);
    }
}
