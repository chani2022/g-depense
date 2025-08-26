<?php

namespace App\Tests\Form;

use App\Form\RegistrationFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class RegistrationFormTypeTest extends TestCase
{
    private ?RegistrationFormType $registrationFormType;

    protected function setUp(): void
    {
        $this->registrationFormType = new RegistrationFormType();
    }

    public function testBuildFormRegistration(): void
    {
        /** @var FormBuilderInterface&Mockobject */
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $options = [];

        $formBuilder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                ['username', TextType::class],
                ['password', RepeatedType::class, [
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Repétez votre mot de passe']
                ]]
            )
            ->willReturnSelf();

        $this->registrationFormType->buildForm($formBuilder, $options);
    }

    public function testConfigureOptionsRegister(): void
    {
        $optionResolver = new OptionsResolver();

        $this->registrationFormType->configureOptions($optionResolver);

        $this->assertEquals([
            'data_class' => User::class
        ], $optionResolver->resolve());
    }



    protected function tearDown(): void
    {
        $this->registrationFormType = null;
    }
}
