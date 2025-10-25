<?php

namespace App\Enum;

enum PenaliteRaison: string
{
    case OVERDUE = 'OVERDUE';
    case LOST    = 'LOST';
    case DAMAGE  = 'DAMAGE';
    case OTHER   = 'OTHER';
}