<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    
                    "Administrateur" => "ROLE_ADMIN"
                ],
                "multiple" => true,
                "expanded" => true
            ])
            ->add('password',PasswordType::class, [
                "label" => "Mot de passe",
                "required" => false,
                "mapped" => false
            ])
            ->add('prenom', TextType::class, [
                "label" => "Prénom",
                "required" => false
            ])
            ->add('nom', TextType::class, [
                "required" => false
            ])
            ->add('email', EmailType::class, [
                "label" => "Prénom",
                "required" => false
            ])
            ->add('adresse')
            ->add('cp', TextType::class, [
                "label" => "Code postal"
                ])
            ->add('ville')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
