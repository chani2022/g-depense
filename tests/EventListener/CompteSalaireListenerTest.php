<?php

namespace App\Tests\EventListener;

use App\Entity\CompteSalaire;
use App\Entity\User;
use App\EventListener\CompteSalaireListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CompteSalaireListenerTest extends TestCase
{
    private ?CompteSalaireListener $compteSalaireListener;

    protected function setUp(): void
    {
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(new UsernamePasswordToken(new User, 'main'));
        $this->compteSalaireListener = new CompteSalaireListener($tokenStorage);
    }
    public function testPrePersistCompteSalaire(): void
    {
        $compteSalaire = new CompteSalaire();
        /** @var ObjectManager|MockObject */
        $mockObjectManager = $this->createMock(EntityManagerInterface::class);
        $prePersistEvent = new PrePersistEventArgs($compteSalaire, $mockObjectManager);

        $this->compteSalaireListener->prePersist($compteSalaire, $prePersistEvent);

        $this->assertInstanceOf(User::class, $compteSalaire->getOwner());
    }

    protected function tearDown(): void
    {
        $this->compteSalaireListener = null;
    }
}
