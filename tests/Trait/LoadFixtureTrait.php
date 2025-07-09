<?php

namespace App\Tests\Trait;

use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\FixtureStore;

trait LoadFixtureTrait
{
    public function getFixtures(): array
    {
        return FixtureStore::getFixtures();
    }
}
