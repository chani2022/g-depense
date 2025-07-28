<?php

namespace App\Tests\Validator;

use App\Validator\UniqueCategory;
use PHPUnit\Framework\TestCase;

class UniqueCategoryTest extends TestCase
{
    private ?UniqueCategory $uniqueCategory;

    protected function setUp(): void
    {
        $this->uniqueCategory = new UniqueCategory();
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
