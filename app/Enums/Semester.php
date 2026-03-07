<?php

namespace App\Enums;

enum Semester: string{
    case FIRST = '1';
    case SECOND = '2';

    public function label(): string
    {
        return match($this) {
            self::FIRST => 'ต้น',
            self::SECOND => 'ปลาย',
        };
    }
}
