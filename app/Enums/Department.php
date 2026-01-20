<?php

namespace App\Enums;

enum Department: string
{
    case CIVIL = 'Civil Engineering';
    case COMPUTER = 'Computer Science';
    case BIOLOGY = 'Biology';

    // The link
    public function faculty(): Faculty
    {
        return match($this) {
            self::CIVIL => Faculty::ENGINEERING,
            self::COMPUTER => Faculty::SCIENCE,
            self::BIOLOGY => Faculty::SCIENCE,
        };
    }
}
