<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de gestion des emprunts.
 * 
 * Ce formulaire permet de créer ou modifier un emprunt avec :
 * - Sélection du membre et de l'exemplaire
 * - Dates de début, de retour prévu et de retour effectif
 * - Statut de l'emprunt et pénalités éventuelles
 */
class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help' => 'Date à laquelle l\'emprunt commence',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de début est obligatoire']),
                ],
            ])
            ->add('dueAt', DateType::class, [
                'label' => 'Date de retour prévue',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help' => 'Date limite de retour de l\'ouvrage',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de retour prévue est obligatoire']),
                ],
            ])
            ->add('returnedAt', DateType::class, [
                'label' => 'Date de retour effectif',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'help' => 'Date de retour réel (laisser vide si non retourné)',
            ])
            ->add('penalty', MoneyType::class, [
                'label' => 'Pénalité',
                'currency' => 'EUR',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00',
                ],
                'help' => 'Montant de la pénalité en cas de retard',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En cours' => 'en_cours',
                    'Retourné' => 'retourne',
                    'En retard' => 'en_retard',
                    'Perdu' => 'perdu',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => '-- Sélectionnez un statut --',
                'help' => 'État actuel de l\'emprunt',
            ])
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user): string {
                    return sprintf('%s (%s)', $user->getNom(), $user->getEmail());
                },
                'label' => 'Membre',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => '-- Sélectionnez un membre --',
                'help' => 'Le membre qui emprunte l\'ouvrage',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Veuillez sélectionner un membre']),
                ],
            ])
            ->add('exemplaire', EntityType::class, [
                'class' => Exemplaires::class,
                'choice_label' => function (Exemplaires $exemplaire): string {
                    $ouvrage = $exemplaire->getOuvrage();
                    $titre = $ouvrage ? $ouvrage->getTitre() : 'Sans titre';
                    return sprintf('%s (Cote: %s)', $titre, $exemplaire->getCote());
                },
                'label' => 'Exemplaire',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => '-- Sélectionnez un exemplaire --',
                'help' => 'L\'exemplaire à emprunter',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Veuillez sélectionner un exemplaire']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
            'attr' => [
                'novalidate' => 'novalidate', // Désactiver la validation HTML5 pour utiliser la validation Symfony
            ],
        ]);
    }
}
