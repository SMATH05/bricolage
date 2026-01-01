<?php

namespace App\Form;

use App\Entity\Chercheur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChercheurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_chercheur')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('mot_de_passe')
            ->add('description')
            ->add('disponibilite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chercheur::class,
        ]);
    }
}
