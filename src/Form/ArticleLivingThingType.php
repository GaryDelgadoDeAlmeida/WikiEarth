<?php

namespace App\Form;

use App\Form\ReferenceType;
use App\Form\LivingThingType;
use App\Form\ArticleContentType;
use App\Entity\ArticleLivingThing;
use Symfony\Component\Form\AbstractType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ArticleLivingThingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('livingThing', LivingThingType::class, [
                'label' => 'Living Thing',
                'mapped' => false
            ])
            ->add('generality', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("generality", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('geography', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("geography", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('ecology', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("ecology", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('behaviour', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("behaviour", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('wayOfLife', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("wayOfLife", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('description', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("description", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('otherData', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // ->add("otherData", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('references', CollectionType::class, [
                "entry_type" => ReferenceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
                'mapped' => false
            ])
            // ->add("references", TinymceType::class, [
            //     "attr" => [
            //         "toolbar" => "bold italic underline | bullist numlist"
            //     ],
            //     'required' => true,
            //     // 'mapped' => false
            // ])
            ->add('mediaGallery', FileType::class, [
                'label' => 'Media Gallery',
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ],
                'mapped' => false,
                "required" => false,
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
            'data_class' => ArticleLivingThing::class,
        ]);
    }
}
