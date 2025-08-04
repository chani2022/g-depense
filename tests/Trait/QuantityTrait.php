<?php

namespace App\Tests\Trait;

use App\Entity\Quantity;

trait QuantityTrait
{
    use LoadFixtureTrait;

    public function getQuantity(): Quantity
    {
        return $this->getFixtures()['quantity_user_credentials_ok'];
    }
}
