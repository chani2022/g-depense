<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Capital;
use App\Entity\Category;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use App\HandleImage\HandleImage;
use App\Repository\CompteSalaireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use GdImage;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EasyAdminSubscriberTest extends TestCase
{
    private ?EasyAdminSubscriber $easyAdminSubscriber;
    /** @var CompteSalaireRepository&MockObject&null */
    private $mockCompteSalaireRepository;
    private ?TokenStorage $tokenStorage;
    private ?HandleImage $handlerImage;

    protected function setUp(): void
    {
        $this->mockCompteSalaireRepository = $this->createMock(CompteSalaireRepository::class);
        $this->tokenStorage = new TokenStorage();
        $this->tokenStorage->setToken(
            new UsernamePasswordToken(new User, 'main')
        );
        $this->handlerImage = new HandleImage(new Imagine(), new Box(40, 40));
        $this->easyAdminSubscriber = new EasyAdminSubscriber($this->tokenStorage, $this->mockCompteSalaireRepository, $this->handlerImage);
    }
    /**
     * ----------------------user-------------------------
     */

    public function testHandleImageInGetSubscribedEvents(): void
    {
        $subscriberEvents = $this->easyAdminSubscriber->getSubscribedEvents();
        $this->assertArrayHasKey(BeforeEntityUpdatedEvent::class, $subscriberEvents);
    }

    public function testHandleImageUser(): void
    {
        $path = $this->simulateCreateImage();
        $user = new User();
        // $tmp = sys_get_temp_dir();
        // $path = $tmp . DIRECTORY_SEPARATOR . 'test.png';

        // $width = 300;
        // $height = 300;
        // $image = imagecreatetruecolor($width, $height);

        // imagepng($image, $path); // 🗂️ Crée le fichier dans ce dossier

        $user->setFile(
            new UploadedFile($path, 'test', 'image/png', null, true)
        );

        $beforeEntityUpdateEvent = new BeforeEntityUpdatedEvent($user);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);
        $eventDispatcher->dispatch($beforeEntityUpdateEvent);

        $imageSize = getimagesize($user->getFile()->getPathname());
        $widthActual = $imageSize[0];
        $heightActual = $imageSize[1];

        $this->assertSame($this->handlerImage->getImage()->getSize()->getWidth(), $widthActual);
        $this->assertSame($this->handlerImage->getImage()->getSize()->getHeight(), $heightActual);

        unlink($path);
    }

    private function simulateCreateImage(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.png';
        $gb = imagecreatetruecolor(300, 300);
        imagepng($gb, $path);

        return $path;
    }
    /**
     * ----------------------compte salaire --------------
     */
    public function testSetOwnerForCompteSalaire(): void
    {
        $compteSalaire = new CompteSalaire();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($compteSalaire);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);
        $eventDispatcher->dispatch($beforeEntityPersistEvent, BeforeEntityPersistedEvent::class);

        $this->assertInstanceOf(User::class, $compteSalaire->getOwner());
    }

    public function testReturnIfObjectNotCompteSalaire(): void
    {
        $compteSalaire = new CompteSalaire();
        $user = new User();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($user);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);
        $eventDispatcher->dispatch($beforeEntityPersistEvent, BeforeEntityPersistedEvent::class);

        $this->assertNull($compteSalaire->getOwner());
    }

    /**
     * ----------------------capital------------------
     */
    public function testSetCompteSalaireForCapital(): void
    {
        $compteSalaire = new CompteSalaire();
        $this->mockCompteSalaireRepository
            ->expects($this->once())
            ->method('getCompteSalaireWithDateNow')
            ->with($this->tokenStorage->getToken()->getUser())
            ->willReturn($compteSalaire);

        $capital = new Capital();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($capital);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);
        $eventDispatcher->dispatch($beforeEntityPersistEvent, BeforeEntityPersistedEvent::class);

        $this->assertInstanceOf(CompteSalaire::class, $capital->getCompteSalaire());
    }


    public function testGetSubscribedEvents(): void
    {
        $actualSubscribedEvents = $this->easyAdminSubscriber->getSubscribedEvents();
        $this->assertArrayHasKey(BeforeEntityPersistedEvent::class, $actualSubscribedEvents);
    }

    /**
     * -------------------------------------------------------------------------------
     * ------------------------------category------------------------------------------
     * -------------------------------------------------------------------------------
     */

    public function testSetOwnerForCategoryInSubscribedEvents(): void
    {
        $subscribeEvents = $this->easyAdminSubscriber->getSubscribedEvents();

        $this->assertSame(['setOwnerForCategory'], $subscribeEvents[BeforeEntityPersistedEvent::class][2]);
    }

    public function testSetOwnerForEntityCategory(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);

        $category = new Category();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($category);
        $eventDispatcher->dispatch($beforeEntityPersistEvent, BeforeEntityPersistedEvent::class);

        $this->assertInstanceOf(User::class, $category->getOwner());
    }

    protected function tearDown(): void
    {
        $this->easyAdminSubscriber = null;
        $this->mockCompteSalaireRepository = null;
        $this->tokenStorage = null;
    }
}
