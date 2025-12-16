<?php

namespace App\Form;

use App\Entity\Exemplaires;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de gestion des réservations.
 * 
 * Ce formulaire permet de créer ou modifier une réservation avec :
 * - Sélection du membre
 * - Sélection de l'exemplaire (optionnel, si spécifique)
 * - Date de création de la réservation
 */
class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creationDate', DateType::class, [
                'label' => 'Date de réservation',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help' => 'Date à laquelle la réservation a été effectuée',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de réservation est obligatoire']),
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user): string {
                    return sprintf('%s (%s)', $user->getNom(), $user->getEmail());
                },
                'label' => 'Membre',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => '-- Sélectionnez un membre --',
                'help' => 'Le membre qui effectue la réservation',
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
                'label' => 'Exemplaire réservé',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => '-- Sélectionnez un exemplaire --',
                'help' => 'L\'exemplaire spécifique à réserver',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
