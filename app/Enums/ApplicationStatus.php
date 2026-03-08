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


    public static function randomWeighted(): self
    {
        return fake()->randomElement([
            self::PENDING, self::PENDING, self::PENDING, self::PENDING,

            self::APPROVED_BY_DEPARTMENT, self::APPROVED_BY_DEPARTMENT,
            self::APPROVED_BY_ASSOCIATE_DEAN,
            self::APPROVED_BY_DEAN,
            self::APPROVED_BY_COMMITTEE,

            self::REJECTED,
        ]);
    }

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
            self::PENDING => 'รอดำเนินการ',
            self::REJECTED => 'ไม่ผ่านการพิจารณา',
            self::APPROVED_BY_DEPARTMENT => 'ผ่านการพิจารณาโดยหัวหน้าภาควิชา',
            self::APPROVED_BY_ASSOCIATE_DEAN => 'ผ่านการพิจารณาโดยรองคณบดี',
            self::APPROVED_BY_DEAN => 'ผ่านการพิจารณาโดยคณบดี',
            self::APPROVED_BY_COMMITTEE => 'ผ่านการพิจารณาโดยคณะกรรมการ',
        };
    }


}
