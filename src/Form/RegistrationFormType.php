<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class RegistrationFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre.email@example.com'],
            ])
            ->add('nom', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'premium-input', 'placeholder' => 'Nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Votre nom doit faire au moins 2 caractères', 'maxMessage' => 'Votre nom ne peut pas dépasser 50 caractères']),
                ],
            ])
            ->add('prenom', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'premium-input', 'placeholder' => 'Prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Votre prénom doit faire au moins 2 caractères', 'maxMessage' => 'Votre prénom ne peut pas dépasser 50 caractères']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'premium-input', 'placeholder' => 'Mot de passe', 'autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Chercheur d\'emploi' => 'ROLE_CHERCHEUR',
                    'Recruteur' => 'ROLE_RECRUTEUR',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions d\'utilisation',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
