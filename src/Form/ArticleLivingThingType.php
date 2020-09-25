<?php

namespace App\Form;

use App\Form\LivingThingType;
use App\Form\ArticleContentType;
use App\Entity\ArticleLivingThing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('title', null, [
                'label' => 'Title'
            ])
            ->add('geography', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('ecology', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('behaviour', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('wayOfLife', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('description', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('otherData', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
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
