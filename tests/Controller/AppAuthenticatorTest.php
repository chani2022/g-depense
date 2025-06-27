<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;

class AppAuthenticatorTest extends TestCase
{
    private AppAuthenticator|null $appAuthenticator;

    protected function setUp(): void {}

    public function testAuth(): void {}

    protected function tearDown(): void
    {
        $this->appAuthenticator = null;
    }
}
