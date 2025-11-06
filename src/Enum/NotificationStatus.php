<?php

namespace App\Enum;

enum NotificationStatus: string
{
    case ATTENTE = 'ATTENTE';
    case ENVOYE = 'ENVOYE';
    case ECHEC = 'ECHEC';
}
