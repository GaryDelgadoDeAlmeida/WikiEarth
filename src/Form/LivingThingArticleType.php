<?php

namespace App\Form;

use App\Entity\ArticleLivingThing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleLivingThingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('geography')
            ->add('ecology')
            ->add('bahaviour')
            ->add('wayOfLife')
            ->add('description')
            ->add('otherData')
            // ->add('approved')
            // ->add('createdAt')
            // ->add('user')
            // ->add('idLivingThing')
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
