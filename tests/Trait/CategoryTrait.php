<?php

namespace App\Tests\Trait;

use App\Entity\Category;

trait CategoryTrait
{
    use LoadFixtureTrait;

    public function getCategory(): Category
    {
        return $this->getFixtures()['category_vital'];
    }

    public function getCategoryOtherOwner(): Category
    {
        return $this->getFixtures()['category_facultatif'];
    }
}
