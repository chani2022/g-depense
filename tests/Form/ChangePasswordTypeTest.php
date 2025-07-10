<?php

namespace App\Tests\Form;

use App\Form\ChangePasswordType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use PHPUnit\Framework\MockObject\MockObject;

class ChangePasswordTypeTest extends TestCase
{
    private ?ChangePasswordType $changePasswordType;

    protected function setUp(): void
    {
        $this->changePasswordType = new ChangePasswordType();
    }

    public function testBuildFormChangePassword(): void
    {
        /** @var MockObject|FormBuilderInterface */
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $options = [];

        $formBuilder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [
                    'oldPassword',
                    PasswordType::class,
                    [
                        'label' => 'Ancien mot de passe',
                        'contraints' => [
                            new UserPassword()
                        ]
                    ]
                ],
                [
                    'newPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => ['attr' => ['class' => 'password-field']],
                        'required' => true,
                        'first_options'  => ['label' => 'Nouveau mot de passe'],
                        'second_options' => ['label' => 'Repetez votre mot de passe'],
                    ]
                ]
            );

        $this->changePasswordType->buildForm($formBuilder, $options);
    }

    protected function tearDown(): void
    {
        $this->changePasswordType = null;
    }
}
