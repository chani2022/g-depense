<?php

namespace App\Tests\Validator;

use App\Validator\UniqueEntityByUser;
use PHPUnit\Framework\TestCase;

class UniqueEntityByUserTest extends TestCase
{
    private ?UniqueEntityByUser $uniqueCategory;

    protected function setUp(): void
    {
        $this->uniqueCategory = new UniqueEntityByUser();
    }

    public function testMessageSameCategory(): void
    {
        $messageExpected = 'Catogory "{{ value }}" already exist.';

        $this->assertSame($messageExpected, $this->uniqueCategory->message);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->uniqueCategory = null;
    }
}
