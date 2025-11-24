<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', null, [
                'widget' => 'single_text'
            ])
            ->add('dueAt', null, [
                'widget' => 'single_text'
            ])
            ->add('returnedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('penalty')
            ->add('status')
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('exemplaire', EntityType::class, [
                'class' => Exemplaires::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
