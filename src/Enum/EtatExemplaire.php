<?php

namespace App\Enum;

enum EtatExemplaire: string
{
    case NEUF = 'neuf';
    case EXCELLENT = 'excellent';
    case BON = 'bon';
    case CORRECT = 'correct';
    case USE = 'usé';
    case ENDOMMAGE = 'endommagé';
    case TRES_MAUVAIS = 'très_mauvais';
    case A_REPARER = 'à_réparer';

    /**
     * Retourne le libellé en français.
     */
    public function getLabel(): string
    {
        return match($this) {
            self::NEUF => 'Neuf',
            self::EXCELLENT => 'Excellent',
            self::BON => 'Bon',
            self::CORRECT => 'Correct',
            self::USE => 'Usé',
            self::ENDOMMAGE => 'Endommagé',
            self::TRES_MAUVAIS => 'Très mauvais',
            self::A_REPARER => 'À réparer',
        };
    }

    /**
     * Retourne la classe CSS Bootstrap pour le badge.
     */
    public function getBadgeClass(): string
    {
        return match($this) {
            self::NEUF, self::EXCELLENT => 'bg-success',
            self::BON, self::CORRECT => 'bg-info',
            self::USE => 'bg-warning',
            self::ENDOMMAGE, self::TRES_MAUVAIS, self::A_REPARER => 'bg-danger',
        };
    }

    /**
     * Retourne tous les états disponibles pour un formulaire.
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
