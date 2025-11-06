<?php

namespace App\Enum;

enum PenaliteRaison: string
{
    case RETARD = 'RETARD';
    case PERDU = 'PERDU';
    case ABIME = 'ABIME';
    case AUTRE = 'AUTRE';
}