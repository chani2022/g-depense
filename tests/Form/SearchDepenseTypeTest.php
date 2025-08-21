<?php

namespace App\Tests\Form;

use App\Form\SearchDepenseType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;

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
                'required' => true,
                'disabled' => true
            ])
            ->willReturnSelf();

        $this->searchDepenseType->buildForm($formBuilder, $options);
    }
}
