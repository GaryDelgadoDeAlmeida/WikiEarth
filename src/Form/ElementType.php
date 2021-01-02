<?php

namespace App\Form;

use App\Entity\Element;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                "label" => "Name",
                "required" => true
            ])
            ->add('scientificName', null, [
                "label" => "Scientific Name",
                "required" => true
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
            ->add('radioisotope', null, [
                "required" => true
            ])
            ->add('atomicNumber', null, [
                "required" => true
            ])
            ->add('symbole', null, [
                "required" => true
            ])
            ->add('atomeGroup', null, [
                "required" => true
            ])
            ->add('atomePeriod', null, [
                "required" => true
            ])
            ->add('atomeBlock', null, [
                "required" => true
            ])
            ->add('volumicMass', null, [
                "required" => true
            ])
            ->add('numCAS', null, [
                "required" => true
            ])
            ->add('numCE', null, [
                "required" => true
            ])
            ->add('atomicMass', null, [
                "required" => true
            ])
            ->add('atomicRadius', null, [
                "required" => true
            ])
            ->add('covalentRadius', null, [
                "required" => true
            ])
            ->add('vanDerWaalsRadius', null, [
                "required" => true
            ])
            ->add('electroniqueConfiguration', null, [
                "required" => true
            ])
            ->add('oxidationState', null, [
                "required" => true
            ])
            ->add('electronegativity', null, [
                "required" => true
            ])
            ->add('fusionPoint', null, [
                "required" => true
            ])
            ->add('boilingPoint', null, [
                "required" => true
            ])
            ->add('radioactivity', null, [
                "required" => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Element::class,
        ]);
    }
}
