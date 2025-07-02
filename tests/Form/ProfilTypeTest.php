<?php

namespace App\Tests\Form;

use App\Form\ProfilType;
use PHPUnit\Framework\TestCase;

class ProfilTypeTest extends TestCase
{
    private ProfilType|null $profilType;

    protected function setUp(): void
    {
        $this->profilType = new ProfilType();
    }

    public function testBuildFormProfil(): void {}

    protected function tearDown(): void
    {
        $this->profilType = null;
    }
}
