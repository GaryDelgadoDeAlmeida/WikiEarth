<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imgPath', FileType::class, [
                'label' => 'Image',
                'required' => false,
                "mapped" => false
            ])
            ->add('firstname', null, [
                'label' => 'Firstname'
            ])
            ->add('lastname', null, [
                'label' => 'Lastname'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('login', null, [
                'label' => 'Login',
                "attr" => [
                    "readonly" => true
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => false,
                "mapped" => false
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirm password',
                'required' => false,
                "mapped" => false
            ])
            ->add('submit', SubmitType::class, [
                "label" => "Update",
                "attr" => [
                    "class" => "btn-custom-green r-20px"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
