<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Capital;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use App\Repository\CompteSalaireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PHPUnit\Framework\MockObject\MockObject;

class EasyAdminSubscriberTest extends TestCase
{
    private ?EasyAdminSubscriber $easyAdminSubscriber;
    /** @var CompteSalaireRepository&MockObject&null */
    private $mockCompteSalaireRepository;

    private ?TokenStorage $tokenStorage;

    protected function setUp(): void
    {
        $this->mockCompteSalaireRepository = $this->createMock(CompteSalaireRepository::class);
        $this->tokenStorage = new TokenStorage();
        $this->tokenStorage->setToken(
            new UsernamePasswordToken(new User, 'main')
        );
        $this->easyAdminSubscriber = new EasyAdminSubscriber($this->tokenStorage, $this->mockCompteSalaireRepository);
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

        $this->assertArrayHasKey(BeforeEntityPersistedEvent::class, $subscribeEvents);

        $this->assertSame(['setOwnerForCategory'], $subscribeEvents[BeforeEntityPersistedEvent::class][2]);
    }

    protected function tearDown(): void
    {
        $this->easyAdminSubscriber = null;
        $this->mockCompteSalaireRepository = null;
        $this->tokenStorage = null;
    }
}
