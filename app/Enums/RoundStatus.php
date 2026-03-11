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

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'เปิด',
            self::CLOSED => 'ปิด',
            self::DRAFT => 'ร่าง',

        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => 'bg-green-100 text-green-700 border-green-200',
            self::CLOSED => 'bg-red-100 text-red-700 border-red-200',
            self::DRAFT => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}
