<?php

namespace App\Enum;

enum NotificationType: string
{
    case MESSAGE = 'SMS';
    case EMAIL = 'EMAIL';
    case INTERNE = 'INTERNE';
}
