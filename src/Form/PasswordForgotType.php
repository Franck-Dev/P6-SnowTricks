<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PasswordForgotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'attr' => [
                    'placeholder' => "Votre nom d'utilisateur ..."
                ],
                'constraints' => [ 
                    new Length(['max' => 50, 'maxMessage' => "Votre nom d'utilisateur ne doit pas dépasser 50 caractères"]), 
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        
        ]);
    }
}
