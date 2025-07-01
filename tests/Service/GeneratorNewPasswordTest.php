<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\GeneratorNewPassword;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GeneratorNewPasswordTest extends TestCase
{
    private GeneratorNewPassword|null $generatorPwd;
    /** @var MockObject&EntityManagerInterface&null */
    private $em;
    /** @var MockObject&UserPasswordHasherInterface&null */
    private $hasher;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->generatorPwd = new GeneratorNewPassword($this->em, $this->hasher);
    }

    public function testRegneratePwd(): void
    {
        $user = new User();
        $newPassword = 'test';

        $newPasswordHashedExpected = '$pass hash';

        $this->hasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, $newPassword)
            ->willReturn($newPasswordHashedExpected);

        $this->generatorPwd->regeneratePwd($user, $newPassword);

        $this->assertEquals($newPasswordHashedExpected, $user->getPassword());
    }

    public function testSave(): void
    {
        $this->em->expects($this->once())
            ->method('flush');

        $this->generatorPwd->save();
    }

    protected function tearDown(): void
    {
        $this->generatorPwd = null;
        $this->em = null;
        $this->hasher = null;
    }
}
