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
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('nom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('file', VichFileType::class, [
                'required' => false,
                'constraints' => [
                    new File(
                        mimeTypes: ['image/png', 'image/jpeg'],
                        mimeTypesMessage: 'Veuillez uploader une image PNG ou JPEG.',
                        maxSize: '4M',
                        maxSizeMessage: 'Le fichier ne doit pas dépasser 4 Mo.'
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
