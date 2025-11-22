<?php

namespace App\Enum;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case LIBRARIAN = 'ROLE_LIBRARIAN';
    case MEMBER = 'ROLE_MEMBER';
}