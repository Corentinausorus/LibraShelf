<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Editeur;
use App\Entity\Ouvrage;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OuvrageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Titre de l\'ouvrage']
            ])
            ->add('ISBN', TextType::class, [
                'label' => 'ISBN',
                'attr' => ['class' => 'form-control', 'placeholder' => 'ISBN']
            ])
            ->add('auteurs', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => 'Nom',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Auteurs',
                'attr' => [
                    'class' => 'form-select',
                    'size' => 5
                ],
                'required' => false,
                'help' => 'Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs auteurs'
            ])
            ->add('editeur', EntityType::class, [
                'class' => Editeur::class,
                'choice_label' => 'nom',
                'label' => 'Éditeur',
                'attr' => ['class' => 'form-select'],
                'placeholder' => '-- Sélectionnez un éditeur --',
                'required' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Catégories',
                'attr' => [
                    'class' => 'form-select',
                    'size' => 4
                ],
                'required' => false,
                'help' => 'Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs catégories'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Tags',
                'attr' => [
                    'class' => 'form-select',
                    'size' => 3
                ],
                'required' => false
            ])
            ->add('Langues', ChoiceType::class, [
                'label' => 'Langues',
                'choices' => [
                    'Français' => 'fr',
                    'Anglais' => 'en',
                    'Espagnol' => 'es',
                    'Allemand' => 'de',
                    'Italien' => 'it',
                    'Portugais' => 'pt',
                    'Néerlandais' => 'nl',
                    'Autre' => 'other'
                ],
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select',
                    'size' => 4
                ],
                'required' => false,
                'mapped' => false
            ])
            ->add('annee', DateType::class, [
                'label' => 'Année de publication',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'mapped' => false
            ])
            ->add('Resume', TextareaType::class, [
                'label' => 'Résumé',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Résumé de l\'ouvrage...'
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ouvrage::class,
        ]);
    }
}
