<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Entity\Chercheur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_candidature')
            ->add('date_pro')
            ->add('statut')
            ->add('chercheur_id', EntityType::class, [
                'class' => Chercheur::class,
                'choice_label' => 'id',
            ])
            ->add('annonce_id', EntityType::class, [
                'class' => Annonce::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
