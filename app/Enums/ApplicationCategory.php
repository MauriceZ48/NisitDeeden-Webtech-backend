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
            self::ACTIVITY => 'Co-curricular',
            self::CREATIVITY    => 'Creativity',
            self::BEHAVIOR  => 'Good Conduct',
        };
    }
}
