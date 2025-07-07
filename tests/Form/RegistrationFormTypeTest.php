<?php

namespace App\Tests\Form;

use App\Form\RegistrationFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;

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

        $formBuilder->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                ['username', TextType::class],
                ['password', RepeatedType::class, [
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



    protected function tearDown(): void
    {
        $this->registrationFormType = null;
    }
}
