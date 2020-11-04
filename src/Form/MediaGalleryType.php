<?php

namespace App\Form;

use App\Entity\MediaGallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MediaGalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MediaGallery::class,
        ]);
    }
}
