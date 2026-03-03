<?php


namespace App\Enums;


enum Domain : string
{
    case BANGKHEN = 'Bangkhen';
    case KAMPHAENG_SEAN = 'Kamphaeng Saen';
    case SRIRACHA = 'Sriracha';
    case CHALERMPHRAKIAT = 'Chalermphrakiat';

    public function label(): string
    {
        return match ($this) {
            self::BANGKHEN =>'บางเขน',
            self::KAMPHAENG_SEAN => 'กำแพงแสน',
            self::SRIRACHA => 'ศรีราชา',
            self::CHALERMPHRAKIAT => 'เฉลิมพระเกียรติ',
        };
    }

}
