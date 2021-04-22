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
                "label" => "Group",
                "required" => false
            ])
            ->add('atomePeriod', null, [
                "label" => "Period",
                "required" => true
            ])
            ->add('atomeBlock', null, [
                "label" => "Block",
                "required" => true
            ])
            ->add('volumicMass', null, [
                "required" => false,
                "mapped" => false,
            ])
            ->add('numCAS', null, [
                "label" => "Num CAS",
                "required" => true
            ])
            ->add('numCE', null, [
                "label" => "Num CE",
                "required" => false,
            ])
            ->add('atomicMass', null, [
                "required" => true
            ])
            ->add('atomicRadius', null, [
                "required" => false
            ])
            ->add('covalentRadius', null, [
                "required" => false
            ])
            ->add('vanDerWaalsRadius', null, [
                "required" => false
            ])
            ->add('electroniqueConfiguration', null, [
                "required" => false
            ])
            ->add('oxidationState', null, [
                "required" => false
            ])
            ->add('electronegativity', null, [
                "required" => false
            ])
            ->add('fusionPoint', null, [
                "required" => false
            ])
            ->add('boilingPoint', null, [
                "required" => false
            ])
            ->add('radioactivity', null, [
                "required" => false
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
