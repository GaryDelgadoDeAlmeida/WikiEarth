<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', null, [
                "label" => "Email",
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add('password', PasswordType::class, [
                "label" => "Password",
                "attr" => [
                    "class" => "inputField"
                ]
            ])
            ->add('submit', SubmitType::class, [
                "label" => "Login",
                "attr" => [
                    "class" => "btn-custom-green"
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
