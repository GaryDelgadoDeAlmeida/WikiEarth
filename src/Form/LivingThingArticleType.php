<?php

namespace App\Form;

use App\Form\LivingThingType;
use App\Entity\ArticleLivingThing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            // ->add('geography')
            ->add('geography_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('geography_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            // ->add('ecology')
            ->add('ecology_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('ecology_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            // ->add('bahaviour')
            ->add('behaviour_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('behaviour_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            // ->add('wayOfLife')
            ->add('wayOfLife_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('wayOfLife_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            // ->add('description')
            ->add('description_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('description_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            // ->add('otherData')
            ->add('otherData_sub_title', null, [
                'label' => 'Sub Title',
                'mapped' => false,
                'required' => false
            ])
            ->add('otherData_sub_content', TextareaType::class, [
                'label' => 'Content',
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleLivingThing::class,
        ]);
    }
}
