<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('file', VichFileType::class, [
                'constraints' => [
                    new File(
                        mimeTypes: ['images/png', 'images/jpeg', 'images/jpg'],
                        maxSize: '4028K'
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
