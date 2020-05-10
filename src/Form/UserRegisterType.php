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
                    "placeholder" => "ex: Marc"
                ]
            ])
            ->add('lastname', null, [
                "label" => "Lastname",
                "attr" => [
                    "placeholder" => "ex: Dupont"
                ]
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "placeholder" => "ex: example@gmail.com"
                ]
            ])
            ->add('password', PasswordType::class, [
                "attr" => [
                    "placeholder" => "ex: Test@84*"
                ]
            ])
            ->add("submit", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-blue rounded"
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
