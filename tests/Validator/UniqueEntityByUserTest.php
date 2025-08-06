<?php

namespace App\Tests\Validator;

use App\Validator\UniqueEntityByUser;
use PHPUnit\Framework\TestCase;

class UniqueEntityByUserTest extends TestCase
{
    private ?UniqueEntityByUser $uniqueEntityByUser;

    protected function setUp(): void
    {
        $this->uniqueEntityByUser = new UniqueEntityByUser();
    }

    public function testMessageSameCategory(): void
    {
        $messageExpected = 'this "{{ value }}" already exist.';

        $this->assertSame($messageExpected, $this->uniqueEntityByUser->message);
    }

    public function testRequiredOptionsSuccess(): void
    {
        $requiredOptionsActual = $this->uniqueEntityByUser->getRequiredOptions();
        $requiredOptionsExpected = [
            'field',
            'entityClass'
        ];
        $this->assertSame($requiredOptionsExpected, $requiredOptionsActual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->uniqueEntityByUser = null;
    }
}
