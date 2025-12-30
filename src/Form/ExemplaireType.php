<?php

namespace App\Form;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Enum\EtatExemplaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExemplaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote', TextType::class, [
                'label' => 'Cote',
                'attr' => [
                    'placeholder' => 'Ex: A-123',
                    'class' => 'form-control',
                ],
                'help' => 'Identifiant unique de l\'exemplaire (lettres, chiffres, tirets)',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La cote ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('etat', EnumType::class, [
                'class' => EtatExemplaire::class,
                'label' => 'État physique',
                'choice_label' => fn(EtatExemplaire $etat) => $etat->getLabel(),
                'attr' => [
                    'class' => 'form-select',
                ],
                'help' => 'État physique actuel de l\'exemplaire',
                'placeholder' => 'Sélectionnez un état',
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible pour emprunt',
                'required' => false,
                'help' => 'Cochez si l\'exemplaire peut être emprunté',
            ])
            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
                'label' => 'Ouvrage associé',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => 'Sélectionnez un ouvrage',
                'help' => 'Livre auquel appartient cet exemplaire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaires::class,
        ]);
    }
}
