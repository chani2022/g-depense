<?php

namespace App\Tests\Form;

use App\Form\SearchDepenseType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDepenseTypeTest extends TestCase
{
    private ?SearchDepenseType $searchDepenseType;

    protected function setUp(): void
    {
        $this->searchDepenseType = new SearchDepenseType();
    }

    protected function tearDown(): void
    {
        $this->searchDepenseType = null;
    }

    public function testSearchDepenseBuild(): void
    {
        $options = [];
        /** @var MockObject|FormBuilderInterface */
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $formBuilder
            ->expects($this->once())
            ->method('add')
            ->with('dates', TextType::class, [
                'label' => 'Recherche',
                'attr' => [
                    'class' => 'form-control search-depense',
                    'readonly' => true
                ]
            ])
            ->willReturnSelf();

        $this->searchDepenseType->buildForm($formBuilder, $options);
    }

    public function testConfigureOptionsSearchDepense(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->searchDepenseType->configureOptions($optionsResolver);

        $this->assertSame([], $optionsResolver->getDefinedOptions());
    }
}
