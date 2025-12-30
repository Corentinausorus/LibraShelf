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
            ->add('cote', TextType::class)
            ->add('etat', EnumType::class, [
                'class' => EtatExemplaire::class,
            ])
            ->add('disponible', CheckboxType::class, [
                'required' => false,
            ])
            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaires::class,
        ]);
    }
}
