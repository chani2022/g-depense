<?php

namespace App\Tests\EventSubscriber;

use App\Entity\CompteSalaire;
use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use PHPUnit\Framework\TestCase;
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

    public function testBeforeEntityPersistEventSuccess(): void
    {
        $compteSalaire = new CompteSalaire();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($compteSalaire);

        $this->easyAdminSubscriber->BeforeEntityPersistedEvent($beforeEntityPersistEvent);

        $this->assertInstanceOf(User::class, $compteSalaire->getOwner());
    }

    public function testBeforeEntityPersistEventReturn(): void
    {
        $user = new user();
        $compteSalaire = new CompteSalaire();
        $beforeEntityPersistEvent = new BeforeEntityPersistedEvent($user);

        $this->easyAdminSubscriber->BeforeEntityPersistedEvent($beforeEntityPersistEvent);

        $this->assertNull($compteSalaire->getOwner());
    }

    public function testGetSubscribedEvents(): void
    {
        $actualSubscribedEvents = $this->easyAdminSubscriber->getSubscribedEvents();
        $this->assertArrayHasKey(BeforeEntityPersistedEvent::class, $actualSubscribedEvents);
    }
}
