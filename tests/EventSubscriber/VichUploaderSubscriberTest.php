<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Category;
use App\Entity\User;
use App\EventSubscriber\VichUploaderSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class VichUploaderSubscriberTest extends TestCase
{
    private ?VichUploaderSubscriber $doctrineSubscriber;
    private ?array $mockImages = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrineSubscriber = new VichUploaderSubscriber();
        $this->mockImages = [];
    }

    protected function tearDown(): void
    {
        if ($this->mockImages) {
            foreach ($this->mockImages as $path) {
                unlink($path);
            }
        }
        $this->mockImages = null;
        $this->doctrineSubscriber = null;
    }

    public function testPreUpdateInGetSubscribedEvents(): void
    {
        $subscribedEvent = $this->doctrineSubscriber->getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_UPLOAD, $subscribedEvent);
    }

    public function testResizeSuccess(): void
    {
        $path = $this->simulateCreateImage();
        $user = $this->simulateUserWithImageToUpload($path);
        $event = $this->simulateEvent($user);

        $this->doctrineSubscriber->onThumbnailImage($event);

        $widthExpected = 50;
        $heightExpected = 50;

        $widthActual = $this->getSizeImage($user->getFile()->getPathname())['width'];
        $heightActual = $this->getSizeImage($user->getFile()->getPathname())['height'];

        $this->assertSame($widthExpected, $widthActual);
        $this->assertSame($heightExpected, $heightActual);
    }

    public function testResizeStop(): void
    {
        $category = new Category();
        $event = $this->simulateEvent($category);

        $this->doctrineSubscriber->onThumbnailImage($event);

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

    /**
     * renvoie le chemin du fichier
     */
    private function simulateCreateImage(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(300, 300);
        imagepng($gb, $path);

        $this->mockImages[] = $path;
        return $path;
    }
}
