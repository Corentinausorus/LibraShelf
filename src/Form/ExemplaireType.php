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

/**
 * Formulaire pour créer/éditer un exemplaire d'ouvrage.
 * 
 * Ce formulaire permet au libraire de :
 * - Attribuer une cote (code de rangement) à l'exemplaire
 * - Définir l'état physique du livre
 * - Indiquer si l'exemplaire est disponible
 * - Associer l'exemplaire à un ouvrage
 */
class ExemplaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Cote : identifiant unique de l'exemplaire dans la bibliothèque
            // Exemple : "A-123", "ROM-045", etc.
            ->add('cote', TextType::class, [
                'label' => 'Cote',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: A-123, ROM-045...',
                    'maxlength' => 10
                ],
                'help' => 'Code de rangement unique (max 10 caractères)'
            ])
            
            // État : condition physique de l'exemplaire
            // On utilise un ChoiceType pour avoir des valeurs standardisées
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
            
            // Disponible : indique si l'exemplaire peut être emprunté
            // Décoché = en cours d'emprunt ou réservé
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible pour emprunt',
                'required' => false,  // Une checkbox peut être décochée
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
                'help' => 'Cochez si l\'exemplaire est disponible pour les membres'
            ])
            
            // Ouvrage : l'ouvrage auquel appartient cet exemplaire
            // On utilise EntityType pour lier à l'entité Ouvrage
            // Le champ est optionnel car on peut pré-remplir via l'URL
            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',  // Affiche le titre dans la liste
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
            // Lie le formulaire à l'entité Exemplaires
            'data_class' => Exemplaires::class,
        ]);
    }
}
