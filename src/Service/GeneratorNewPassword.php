<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GeneratorNewPassword
{
    private User|null $user;

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, ?User $user = null)
    {
        $this->user = $user ?? null;
    }

    public function regeneratePwd(User $user, string $newPassword): static
    {
        $newPasswordHashed = $this->hasher->hashPassword($user, $newPassword);
        $user->setPassword($newPasswordHashed);

        $this->user = $user;
        return $this;
    }

    public function save(): void
    {
        $this->em->flush();
    }
}
