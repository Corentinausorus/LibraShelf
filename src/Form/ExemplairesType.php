<?php

namespace App\Form;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Enum\EtatExemplaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExemplairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote', TextType::class, [
                'label' => 'Cote',
                'attr' => [
                    'placeholder' => 'Ex: ROM-001',
                    'class' => 'form-control',
                ],
            ])
            ->add('etat', EnumType::class, [
                'label' => 'État de l\'exemplaire',
                'class' => EtatExemplaire::class,
                'choice_label' => fn(EtatExemplaire $etat) => $etat->getLabel(),
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
                'label' => 'Ouvrage',
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaires::class,
        ]);
    }
}
