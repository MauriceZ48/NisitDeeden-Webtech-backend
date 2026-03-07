<?php


namespace App\Enums;

enum UserPosition: string
{
    case DEAN = 'dean';
    case ASSOCIATE_DEAN = 'associate_dean';
    case HEAD_OF_DEPARTMENT = 'head_of_department';
    case COMMITTEE_MEMBER = 'committee_member';
    case STUDENT = 'student';
    case STAFF = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::DEAN => 'คณบดี',
            self::ASSOCIATE_DEAN => 'รองคณบดี',
            self::HEAD_OF_DEPARTMENT => 'หัวหน้าภาควิชา',
            self::COMMITTEE_MEMBER => 'คณะกรรมการ',
            self::STUDENT => 'นิสิต',
            self::STAFF => 'กองพัฒนานิสิต',
        };
    }

    public function getRole(): UserRole
    {
        return match($this) {
            self::STUDENT => UserRole::STUDENT,
            self::STAFF   => UserRole::ADMIN,
            default       => UserRole::COMMITTEE,
        };
    }
}
