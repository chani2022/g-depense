<?php

namespace App\Tests\Form;

use App\Entity\Quantity;
use App\Form\QuantityType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $constraints = [
            new NotBlank()
        ];
        $mockFormBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [
                    'unite',
                    TextType::class,
                    [
                        'constraints' => $constraints
                    ]
                ],
                [
                    'quantite',
                    NumberType::class,
                    [
                        'constraints' => $constraints
                    ]
                ]
            )
            ->willReturnSelf();

        $this->quantityType->buildForm($mockFormBuilder, $mockOptions);
    }

    public function testConfigureOptionsQuantity(): void
    {
        /** @var MockObject|OptionsResolver */
        $mockResolver = $this->createMock(OptionsResolver::class);
        $mockResolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with([
                'data_class' => Quantity::class
            ])
            ->willReturnSelf();


        $this->quantityType->configureOptions($mockResolver);
    }
}
