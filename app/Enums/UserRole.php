<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case STUDENT = 'STUDENT';
    case COMMITTEE = 'COMMITTEE';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'ผู้ดูแลระบบ',
            self::STUDENT => 'นิสิต',
            self::COMMITTEE => 'ผู้ประเมิน',
        };
    }

}
