<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Capital;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EasyAdminSubscriberTest extends TestCase
{
    private ?EasyAdminSubscriber $easyAdminSubscriber;

    protected function setUp(): void
    {
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(
            new UsernamePasswordToken(new User, 'main')
        );
        $this->easyAdminSubscriber = new EasyAdminSubscriber($tokenStorage);
    }
    /**
     * ----------------------compte salaire --------------
     */
    public function testBeforeEntityCompteSalairePersistEventSuccess(): void
    {
        $compteSalaire = new CompteSalaire();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($compteSalaire);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->easyAdminSubscriber);
        $eventDispatcher->dispatch($beforeEntityPersistEvent, BeforeEntityPersistedEvent::class);

        $this->assertInstanceOf(User::class, $compteSalaire->getOwner());
    }

    public function testBeforeEntityCompteSalairePersistEventReturn(): void
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
    public function testBeforeEntityCapitalPersistEventSuccess(): void
    {
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
}
