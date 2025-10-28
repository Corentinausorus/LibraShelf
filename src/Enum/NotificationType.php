<?php

namespace App\Enum;

enum NotificationType: string
{
    case MESSAGE = 'sms';
    case EMAIL = 'EMAIL';
    case INTERNAL = 'INTERNAL';
}
