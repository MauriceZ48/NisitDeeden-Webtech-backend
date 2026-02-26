<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case PENDING = 'PENDING';

    case APPROVED_BY_DEPARTMENT = 'APPROVED_BY_DEPARTMENT';
    case APPROVED_BY_ASSOCIATE_DEAN = 'APPROVED_BY_ASSOCIATE_DEAN';
    case APPROVED_BY_DEAN = 'APPROVED_BY_DEAN';
    case APPROVED_BY_COMMITTEE = 'APPROVED_BY_COMMITTEE';
    case REJECTED = 'REJECTED';

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            self::REJECTED => 'bg-red-100 text-red-700 border-red-200',
            self::APPROVED_BY_DEPARTMENT,
            self::APPROVED_BY_ASSOCIATE_DEAN,
            self::APPROVED_BY_COMMITTEE,
            self::APPROVED_BY_DEAN => 'bg-green-100 text-green-700 border-green-200',
        };
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::REJECTED => 'Rejected',
            self::APPROVED_BY_DEPARTMENT => 'Dept Approved',
            self::APPROVED_BY_ASSOCIATE_DEAN => 'Dean Office Approved',
            self::APPROVED_BY_COMMITTEE => 'Committee Approved',
            self::APPROVED_BY_DEAN => 'Faculty Approved',
        };
    }

}
