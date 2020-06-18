<?php

namespace App\Form;

use App\Entity\Animal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commonName', null, [
                'label' => "Common Name",
                "required" => false
            ])
            ->add('name', null, [
                'label' => "Name",
                "required" => false
            ])
            ->add('kingdom', null, [
                'label' => "Kingdom",
                "required" => false
            ])
            ->add('subKingdom', null, [
                'label' => "Sub Kingdom",
                "required" => false
            ])
            ->add('domain', null, [
                'label' => "Domain",
                "required" => false
            ])
            ->add('branch', null, [
                'label' => "Branch",
                "required" => false
            ])
            ->add('subBranch', null, [
                'label' => "Sub Branch",
                "required" => false
            ])
            ->add('infraBranch', null, [
                'label' => "Infra Branch",
                "required" => false
            ])
            ->add('division', null, [
                'label' => "Division",
                "required" => false
            ])
            ->add('superClass', null, [
                'label' => "Super Class",
                "required" => false
            ])
            ->add('class', null, [
                'label' => "Class",
                "required" => false
            ])
            ->add('subClass', null, [
                'label' => "Sub Class",
                "required" => false
            ])
            ->add('infraClass', null, [
                'label' => "Infra Class",
                "required" => false
            ])
            ->add('superOrder', null, [
                'label' => "Super Order",
                "required" => false
            ])
            ->add('normalOrder', null, [
                'label' => "Order",
                "required" => false
            ])
            ->add('subOrder', null, [
                'label' => "Sub Order",
                "required" => false
            ])
            ->add('infraOrder', null, [
                'label' => "Infra Order",
                "required" => false
            ])
            ->add('microOrder', null, [
                'label' => "Micro Order",
                "required" => false
            ])
            ->add('superFamily', null, [
                'label' => "Super Family",
                "required" => false
            ])
            ->add('family', null, [
                'label' => "Family",
                "required" => false
            ])
            ->add('subFamily', null, [
                'label' => "Sub Family",
                "required" => false
            ])
            ->add('genus', null, [
                'label' => "Ganus",
                "required" => false
            ])
            ->add('subGenus', null, [
                'label' => "Sub Genus",
                "required" => false
            ])
            ->add('species', null, [
                'label' => "Species",
                "required" => false
            ])
            ->add('subSpecies', null, [
                'label' => "Sub Species",
                "required" => false
            ])
            // ->add('animalType', null, [
            //     'label' => "Animal Type",
            //     "required" => true
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
