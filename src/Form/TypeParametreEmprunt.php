<?php

namespace App\Form;

use App\Entity\ParametreEmprunt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeParametreEmprunt extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('empruntDureeJours')
            ->add('penaliteCentimesParJour')
            ->add('joursTolerance')
            ->add('Configuration', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParametreEmprunt::class,
        ]);
    }
}
