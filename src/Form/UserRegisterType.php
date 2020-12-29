<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, [
                "label" => "Firstname",
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add('lastname', null, [
                "label" => "Lastname",
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add('password', PasswordType::class, [
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Register",
                "attr" => [
                    "class" => "btn-custom-midnight"
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
