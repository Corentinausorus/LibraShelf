<?php

namespace App\Enum;

enum StatutReservation: string
{
    case EN_ATTENTE = 'en_attente';
    case DISPONIBLE = 'disponible';
    case A_RECUPERER = 'à_récupérer';
    case ANNULEE = 'annulée';
    case TERMINEE = 'terminée';
    case EXPIREE = 'expirée';

    /**
     * Retourne le libellé en français.
     */
    public function getLabel(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::DISPONIBLE => 'Disponible',
            self::A_RECUPERER => 'À récupérer',
            self::ANNULEE => 'Annulée',
            self::TERMINEE => 'Terminée',
            self::EXPIREE => 'Expirée',
        };
    }

    /**
     * Retourne la classe CSS Bootstrap pour le badge.
     */
    public function getBadgeClass(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'bg-warning',
            self::DISPONIBLE, self::A_RECUPERER => 'bg-success',
            self::ANNULEE => 'bg-secondary',
            self::TERMINEE => 'bg-info',
            self::EXPIREE => 'bg-danger',
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
