<?php

namespace App\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;

class VichUploaderSubscriberTest extends TestCase
{
    private ?VichUploaderSubscriber $vichUploaderSubscriber;

    protected function setUp(): void {}

    protected function tearDown(): void
    {
        $this->vichUploaderSubscriber = null;
    }
}
