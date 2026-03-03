<?php

namespace App\Enums;

enum RoundStatus: string{
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';

    case DRAFT = 'DRAFT';

    public static function selectableCases(): array
    {
        return [self::OPEN, self::CLOSED];
    }
}
