<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Mineral;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MineralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                "label" => "Scientific Name"
            ])
            ->add('imgPath', FileType::class, [
                'label' => "Photo",
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],
                "required" => false,
                'mapped' => false
            ])
            ->add('rruffChemistry', null, [
                "label" => "RRUFF Chemistry",
                "required" => true
            ])
            ->add('imaChemistry', null, [
                "label" => "IMA Chemistry",
                "required" => true,
            ])
            ->add('chemistryElements', null, [
                "label" => "Chemistry Elements"
            ])
            ->add('imaNumber', null, [
                "label" => "IMA Number",
                "required" => false
            ])
            ->add('imaStatus', null, [
                "label" => "IMA Status",
                "mapped" => false,
            ])
            ->add('structuralGroupname', null, [
                "label" => "Structural Groupname",
                "required" => false
            ])
            ->add('crystalSystem', null, [
                "label" => "Crystal System"
            ])
            ->add('valenceElements', null, [
                "label" => "Valence Elements"
            ])
            ->add('country', EntityType::class, [
                'label' => "Countrys",
                "class" => Country::class,
                "choice_label" => "name",
                'multiple' => true,
                "required" => false,
                "mapped" => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mineral::class,
        ]);
    }
}
