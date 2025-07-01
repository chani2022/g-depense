<?php

namespace App\Tests\Trait;

use Hautelook\AliceBundle\PhpUnit\FixtureStore;

trait LoadFixtureTrait
{
    public function getFixtures(): array
    {
        return FixtureStore::getFixtures();
    }
}
