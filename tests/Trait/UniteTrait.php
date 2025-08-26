<?php

namespace App\Tests\Trait;

use App\Entity\Unite;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

trait UniteTrait
{
    use LoadFixtureTrait;

    public function getUnite(): Unite
    {
        return $this->getFixtures()['unite_user_credentials_ok'];
    }

    public function getUniteOtherOwner(): Unit
    {
        return $this->getFixtures()['unite_user_credentials_ok_other'];
    }
}
