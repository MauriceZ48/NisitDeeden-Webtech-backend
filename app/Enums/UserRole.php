<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case STUDENT = 'STUDENT';
    case COMMITTEE = 'COMMITTEE';

}
