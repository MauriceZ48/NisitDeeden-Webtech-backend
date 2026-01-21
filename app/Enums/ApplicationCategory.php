<?php

namespace App\Enums;

enum ApplicationCategory:string
{
    case ACTIVITY = 'ACTIVITY';
    case CREATIVITY = 'CREATIVITY';
    case BEHAVIOR = 'BEHAVIOR';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVITY => 'ACTIVITY',
            self::CREATIVITY    => 'CREATIVITY',
            self::BEHAVIOR  => 'BEHAVIOR',
        };
    }
}
