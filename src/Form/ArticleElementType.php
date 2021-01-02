<?php

namespace App\Form;

use App\Form\ElementType;
use App\Entity\ArticleElement;
use App\Form\ArticleContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ArticleElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('element', ElementType::class, [
                "label" => "Element",
                "mapped" => false
            ])
            ->add('generality', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true
            ])
            ->add('description', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true
            ])
            ->add('characteristics', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true
            ])
            ->add('property', CollectionType::class, [
                "entry_type" => ArticleContentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true
            ])
            ->add('utilization', CollectionType::class, [
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
            'data_class' => ArticleElement::class,
        ]);
    }
}
