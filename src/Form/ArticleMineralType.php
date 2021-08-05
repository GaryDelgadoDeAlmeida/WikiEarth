<?php

namespace App\Form;

use App\Form\MineralType;
use App\Form\ReferenceType;
use App\Entity\ArticleMineral;
use App\Form\ArticleContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class ArticleMineralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mineral', MineralType::class, [
                'label' => 'Mineral',
                'mapped' => false
            ])
            ->add('generality', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('etymology', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('properties', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('geology', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('mining', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('references', CollectionType::class, [
                "entry_type" => ReferenceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
                'mapped' => false
            ])
            ->add('mediaGallery', FileType::class, [
                'label' => 'Media Gallery',
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ],
                'constraints' => [
                    new Count(['max' => 5]),
                    new All([
                        new File([
                            'maxSize' => '5Mi',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/jpeg',
                                'image/png',
                            ],
                        ])
                    ])
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-custom-blue"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleMineral::class,
        ]);
    }
}
