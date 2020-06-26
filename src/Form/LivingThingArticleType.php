<?php

namespace App\Form;

use App\Form\AnimalType;
use App\Form\SourceLinkType;
use App\Form\MediaGalleryType;
use App\Form\ArticleContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LivingThingArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => "Title",
                'required' => true
            ])
            ->add('animalPhoto', MediaGalleryType::class, [
                'mapped' => false
            ])
            ->add('animal', AnimalType::class, [
                'mapped' => false
            ])
            ->add('caracteristique', ArticleContentType::class, [
                "label" => "Caracteristique",
                'required' => true,
                'mapped' => false
            ])
            ->add('comportement', ArticleContentType::class, [
                "label" => "Comportement",
                'required' => true,
                'mapped' => false
            ])
            ->add('ecologie', ArticleContentType::class, [
                "label" => "Ecologie",
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_1', SourceLinkType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_2', SourceLinkType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_3', SourceLinkType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
