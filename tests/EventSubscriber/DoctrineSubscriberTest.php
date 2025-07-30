<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Category;
use App\Entity\User;
use App\EventSubscriber\DoctrineSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\Persistence\ObjectManager;
use Imagine\Gd\Imagine;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class DoctrineSubscriberTest extends TestCase
{
    private ?DoctrineSubscriber $doctrineSubscriber;
    /** @var  ObjectManager&MockObject&null*/
    private $objectManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->doctrineSubscriber = new DoctrineSubscriber();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->doctrineSubscriber = null;
        $this->objectManager = null;
    }

    public function testPreUpdateInGetSubscribedEvents(): void
    {
        $subscribedEvent = $this->doctrineSubscriber->getSubscribedEvents();

        $this->assertContains(Events::preUpdate, $subscribedEvent);
    }

    public function testResizeSuccess(): void
    {
        $path = $this->simulateCreateImage();
        $user = $this->simulateUserWithImageToUpload($path);
        $event = $this->simulateEvent($user);

        $this->doctrineSubscriber->onPostUpload($event);

        $widthExpected = 50;
        $heightExpected = 50;

        $widthActual = $this->getSizeImage($user->getFile()->getPathname())['width'];
        $heightActual = $this->getSizeImage($user->getFile()->getPathname())['height'];

        $this->assertSame($widthExpected, $widthActual);
        $this->assertSame($heightExpected, $heightActual);

        unlink($path);
    }

    public function testResizeStop(): void
    {
        $category = new Category();
        $event = $this->simulateEvent($category);

        $this->doctrineSubscriber->onPostUpload($event);

        $this->assertTrue(true);
    }

    private function simulateUserWithImageToUpload(string $pathFile): User
    {
        $user = new User();
        $uploadedFile = new UploadedFile($pathFile, 'test.png', 'image/png', null, true);
        $user->setFile($uploadedFile);

        return $user;
    }

    private function simulateEvent(object $object): Event
    {
        $mapping = new PropertyMapping('file', 'imageName');
        $event = new Event($object, $mapping);

        return $event;
    }

    private function getSizeImage(string $pathname): array
    {
        $imageSize = getimagesize($pathname);

        return [
            'width' => $imageSize[0],
            'height' => $imageSize[1]
        ];
    }

    private function simulateCreateImage(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(300, 300);
        imagepng($gb, $path);

        return $path;
    }
}
