<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\ProfilType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProfilTypeTest extends TestCase
{
    /** @var MockObject&FormBuilderInterface&null  */
    private $formBuilder;
    private ProfilType|null $profilType;

    protected function setUp(): void
    {
        $this->profilType = new ProfilType();
        $this->formBuilder = $this->createMock(FormBuilderInterface::class);
    }

    public function testBuildFormProfil(): void
    {
        $this->formBuilder->expects($this->exactly(4))
            ->method('add')
            ->withConsecutive(
                [
                    'username',
                    TextType::class,
                    [
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]
                ],
                [
                    'nom',
                    TextType::class,
                    [
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]
                ],
                [
                    'prenom',
                    TextType::class,
                    [
                        'constraints' => [
                            new NotBlank()
                        ]
                    ]
                ],
                [
                    'file',
                    VichFileType::class,
                    [
                        'constraints' => [
                            new File(
                                mimeTypes: ['images/png', 'images/jpeg', 'images/jpg'],
                                maxSize: '4028K'
                            )
                        ]
                    ]
                ],

            )
            ->willReturnSelf();

        $this->profilType->buildForm($this->formBuilder, []);
    }

    public function testConfigureOptionsProfil(): void
    {
        $optionResolver = new OptionsResolver();

        $this->profilType->configureOptions($optionResolver);

        $this->assertEquals(
            ['data_class' => User::class],
            $optionResolver->resolve()
        );
    }

    protected function tearDown(): void
    {
        $this->profilType = null;
        $this->formBuilder = null;
    }
}
