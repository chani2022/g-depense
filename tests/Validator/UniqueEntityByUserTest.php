<?php

namespace App\Tests\Validator;

use App\Validator\UniqueEntityByUser;
use PHPUnit\Framework\TestCase;

class UniqueEntityByUserTest extends TestCase
{
    private ?UniqueEntityByUser $uniqueEntityByUser;

    protected function setUp(): void
    {
        $this->uniqueEntityByUser = new UniqueEntityByUser(field: 'test', entityClass: 'test');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->uniqueEntityByUser = null;
    }

    public function testMessageSameUniqueEntityByUser(): void
    {
        $messageExpected = 'this "{{ value }}" already exist.';

        $this->assertSame($messageExpected, $this->uniqueEntityByUser->message);
    }

    public function testRequiredOptionsSuccess(): void
    {
        $requiredOptionsActual = $this->uniqueEntityByUser->getRequiredOptions();
        $requiredOptionsExpected = [
            'field',
            'entityClass',
            'mappingOwner'
        ];
        $this->assertSame($requiredOptionsExpected, $requiredOptionsActual);
    }

    public function testTargetsUniqueEntityByUser(): void
    {
        $targetActual = $this->uniqueEntityByUser->getTargets();
        $targetExpected = $this->uniqueEntityByUser::CLASS_CONSTRAINT;

        $this->assertSame($targetExpected, $targetActual);
    }
}
