<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudo')
        
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Le mot de passe saisi ne correspond pas',
            'label' => 'Votre mot de passe',
            'required' => true,
            'first_options' => [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre mot de passe'
                ]],
            'second_options' => [
                'label' => 'Confirmation du mot de passe',
                'attr' => [
                    'placeholder' => 'Confirmez votre mot de passe'
                ]],
        ])

        ->add('prenom', TextType::class, [
            "label" => "Prénom",
        
        ])
        ->add('nom', TextType::class, [
        
        ])

        ->add('email', EmailType::class, [

        ])
        
        ->add('adresse', TextType::class, [
        
            ])

        ->add('cp', TextType::class, [
            'label' => 'Code postal',
        ])

        ->add('ville', TextType::class, [
            
        ])

        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter nos condition d\'utilisation',
                ]),
            ],
            "label" => "C.G.U"
        ])

        ->add('inscrire', SubmitType::class,[
            "label" => "S'inscrire",
            "attr" => [
                "class" => "btn amado-btn w-100"
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
