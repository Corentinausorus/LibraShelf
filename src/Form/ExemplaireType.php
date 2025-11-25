<?php

namespace App\Form;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExemplaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote', TextType::class, [
                'label' => 'Cote',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: A-123, ROM-045...',
                    'maxlength' => 10
                ],
                'help' => 'Code de rangement unique (max 10 caractères)'
            ])
            

            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Très bon' => 'Très bon',
                    'Bon' => 'Bon',
                    'Acceptable' => 'Acceptable',
                    'Usé' => 'Usé',
                    'Endommagé' => 'Endommagé',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => '-- Sélectionnez l\'état --',
                'help' => 'État physique actuel de l\'exemplaire'
            ])
            

            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible pour emprunt',
                'required' => false,  
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
                'help' => 'Cochez si l\'exemplaire est disponible pour les membres'
            ])
            

            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',  
                'label' => 'Ouvrage',
                'attr' => ['class' => 'form-select'],
                'placeholder' => '-- Sélectionnez un ouvrage --',
                'required' => true,
                'help' => 'L\'ouvrage auquel appartient cet exemplaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

            'data_class' => Exemplaires::class,
        ]);
    }
}
