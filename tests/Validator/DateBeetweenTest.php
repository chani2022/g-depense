<?php

namespace App\Tests\Validator;

use App\Validator\DateBeetween;
use PHPUnit\Framework\TestCase;

class DateBeetweenTest extends TestCase
{
    private ?DateBeetween $dateBetween;

    protected function setUp(): void
    {
        $this->dateBetween = new DateBeetween('strict');
    }

    public function testMessageEqual(): void
    {
        $messageExpected = 'Cette date "{{ value }}" est dans une compte! veuillez choisir une autre.';

        $this->assertEquals($messageExpected, $this->dateBetween->message);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dateBetween = null;
    }
}
