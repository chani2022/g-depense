<?php

namespace App\Tests\Form;

use App\Form\ForgotPasswordType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgotPasswordTypeTest extends TestCase
{
    /** @var  FormBuilderInterface&MockObject&null */
    private $builder;
    /** @var OptionsResolver&null */
    private $optionResolver;

    private ForgotPasswordType|null $forgotPasswordType;

    protected function setUp(): void
    {
        $this->builder = $this->createMock(FormBuilderInterface::class);
        $this->forgotPasswordType = new ForgotPasswordType();
        $this->optionResolver = new OptionsResolver();
    }

    public function testBuildForgotPasswordForm(): void
    {
        $this->builder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                ['username', TextType::class, [
                    "constraints" => [
                        new NotBlank()
                    ]
                ]],
                ['envoyer', SubmitType::class, []]
            )
            ->willReturnSelf();

        $this->forgotPasswordType->buildForm($this->builder, []);
    }

    public function testConfigureOptions(): void
    {
        $this->forgotPasswordType->configureOptions($this->optionResolver);
        $this->assertEquals([], $this->optionResolver->getDefinedOptions());
    }

    protected function tearDown(): void
    {
        $this->forgotPasswordType = null;
        $this->builder = null;
        $this->optionResolver = null;
    }
}
