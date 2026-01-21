<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'mapped' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis']),
                    new Length(['min' => 2, 'max' => 100, 'minMessage' => 'Le prénom doit avoir au moins 2 caractères']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'mapped' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis']),
                    new Length(['min' => 2, 'max' => 100, 'minMessage' => 'Le nom doit avoir au moins 2 caractères']),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre.email@example.com'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est requis']),
                    new Email(['message' => 'L\'adresse email n\'est pas valide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre mot de passe', 'autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est requis']),
                    new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit avoir au moins 6 caractères']),
                ],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Chercheur d\'emploi' => 'ROLE_CHERCHEUR',
                    'Recruteur' => 'ROLE_RECRUTEUR',
                ],
                'label' => 'Rôle',
                'mapped' => false,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un rôle']),
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
