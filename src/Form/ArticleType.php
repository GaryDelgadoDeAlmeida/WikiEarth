<?php

namespace App\Form;

use App\Form\SourceLinkType;
use App\Form\ArticleContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => "Title",
                'required' => true
            ])
            ->add('postContent_1', ArticleContentType::class, [
                "label" => "Content",
                'required' => true,
                'mapped' => false
            ])
            ->add('postContent_2', ArticleContentType::class, [
                "label" => "Content",
                'required' => true,
                'mapped' => false
            ])
            ->add('postContent_3', ArticleContentType::class, [
                "label" => "Content",
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_1', SourceLinkType::class, [
                'label' => "Source Link",
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_2', SourceLinkType::class, [
                'label' => "Source Link",
                'required' => true,
                'mapped' => false
            ])
            ->add('postSourceLink_3', SourceLinkType::class, [
                'label' => "Source Link",
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
