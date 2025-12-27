<?php

namespace App\Enum;

enum StatutEmprunt: string
{
    case EN_COURS = 'en_cours';
    case EN_RETARD = 'en_retard';
    case RETOURNE = 'retourné';
    case ANNULE = 'annulé';

    /**
     * Retourne le libellé en français.
     */
    public function getLabel(): string
    {
        return match($this) {
            self::EN_COURS => 'En cours',
            self::EN_RETARD => 'En retard',
            self::RETOURNE => 'Retourné',
            self::ANNULE => 'Annulé',
        };
    }

    /**
     * Retourne la classe CSS Bootstrap pour le badge.
     */
    public function getBadgeClass(): string
    {
        return match($this) {
            self::EN_COURS => 'bg-primary',
            self::EN_RETARD => 'bg-danger',
            self::RETOURNE => 'bg-success',
            self::ANNULE => 'bg-secondary',
        };
    }

    /**
     * Retourne tous les statuts disponibles pour un formulaire.
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }
        return $choices;
    }
}
