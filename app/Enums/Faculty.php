<?php

namespace App\Enums;

enum Faculty: string
{
    case ENGINEERING = 'Engineering';
    case SCIENCE = 'Science';
    case AGRICULTURE = 'Agriculture';
    case BUSINESS_ADMIN = 'Business Administration';
    case SOCIAL_SCIENCES = 'Social Sciences';
    case HUMANITIES = 'Humanities';
    case EDUCATION = 'Education';

    public function label(): string
    {
        return match($this) {
            self::ENGINEERING => 'วิศวกรรมศาสตร์',
            self::SCIENCE => 'วิทยาศาสตร์',
            self::AGRICULTURE => 'เกษตร',
            self::BUSINESS_ADMIN => 'บริหารธุรกิจ',
            self::SOCIAL_SCIENCES => 'สังคมศาสตร์',
            self::HUMANITIES => 'มนุษยศาสตร์',
            self::EDUCATION => 'ศึกษาศาสตร์',
        };
    }
}
