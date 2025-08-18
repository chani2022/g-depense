<?php

namespace App\Tests\Form;

use App\Form\QuantityType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;

class QuantityTypeTest extends TestCase
{
    private ?QuantityType $quantityType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quantityType = new QuantityType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->quantityType = null;
    }

    public function testBuildFormQuantity(): void
    {
        /** @var MockObject|FormBuilderInterface */
        $mockFormBuilder = $this->createMock(FormBuilderInterface::class);
        $mockOptions = [];

        $mockFormBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive([
                ['unite'],
                ['quantity']
            ])
            ->willReturnSelf();

        $this->quantityType->buildForm($mockFormBuilder, $mockOptions);
    }
}
