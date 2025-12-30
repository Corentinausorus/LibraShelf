<?php

namespace App\Enum;

enum EtatExemplaire: string
{
    case NEUF = 'neuf';
    case BON = 'bon';
    case MOYEN = 'moyen';
    case MAUVAIS = 'mauvais';
    case DETERIORE = 'deteriore';

    /**
     * Retourne le label lisible de l'état.
     */
    public function getLabel(): string
    {
        return match($this) {
            self::NEUF => 'Neuf',
            self::BON => 'Bon état',
            self::MOYEN => 'État moyen',
            self::MAUVAIS => 'Mauvais état',
            self::DETERIORE => 'Détérioré',
        };
    }

    /**
     * Retourne la classe CSS Bootstrap pour le badge.
     */
    public function getBadgeClass(): string
    {
        return match($this) {
            self::NEUF => 'bg-success',
            self::BON => 'bg-primary',
            self::MOYEN => 'bg-warning',
            self::MAUVAIS => 'bg-danger',
            self::DETERIORE => 'bg-dark',
        };
    }
}
