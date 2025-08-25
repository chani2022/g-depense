<?php

namespace App\Tests\Trait;

use App\Entity\User;

trait UserAuthenticatedTrait
{
    use LoadFixtureTrait;

    public function getSimpeUserAuthenticated(): User
    {
        return $this->getFixtures()['user_credentials_ok'];
    }

    public function getSimpeOtherUserAuthenticated(): User
    {
        return $this->getFixtures()['user_credentials_other'];
    }

    public function getAdminAuthenticated(): User
    {
        return $this->getFixtures()['user_admin'];
    }
}
