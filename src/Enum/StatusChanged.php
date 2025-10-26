<?php

namespace App\Enum;

enum StatusChanged: string
{
    case STATUS_CHANGED = 'Statut modifié';
    case CONDITION_CHANGED = 'Condition modifiée';
    case LOCATION_CHANGED = 'Emplacement modifié';
}