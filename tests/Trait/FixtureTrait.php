<?php

namespace App\Tests\Trait;

use Hautelook\AliceBundle\PhpUnit\FixtureStore;

trait FixtureTrait
{
    public function getFixtures()
    {
        self::bootKernel();
    }
}
