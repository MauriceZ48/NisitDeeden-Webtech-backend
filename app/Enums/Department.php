<?php

namespace App\Enums;

enum Department: string
{
    // Engineering
    case CIVIL = 'Civil Engineering';
    case MECHANICAL = 'Mechanical Engineering';
    case ELECTRICAL = 'Electrical Engineering';

    // Science
    case COMPUTER = 'Computer Science';
    case BIOLOGY = 'Biology';
    case PHYSICS = 'Physics';
    case CHEMISTRY = 'Chemistry';

    // Business
    case ACCOUNTING = 'Accounting';
    case MARKETING = 'Marketing';
    case MANAGEMENT = 'Management';

    // Others
    case AGRONOMY = 'Agronomy'; // ภาควิชาพืชไร่นา
    case PSYCHOLOGY = 'Psychology'; // ภาควิชาจิตวิทยา
    case LINGUISTICS = 'Linguistics'; // ภาควิชาภาษาศาสตร์

    public function faculty(): Faculty
    {
        return match($this) {
            self::CIVIL, self::MECHANICAL, self::ELECTRICAL => Faculty::ENGINEERING,
            self::COMPUTER, self::BIOLOGY, self::PHYSICS, self::CHEMISTRY => Faculty::SCIENCE,
            self::ACCOUNTING, self::MARKETING, self::MANAGEMENT => Faculty::BUSINESS_ADMIN,
            self::AGRONOMY => Faculty::AGRICULTURE,
            self::PSYCHOLOGY => Faculty::SOCIAL_SCIENCES,
            self::LINGUISTICS => Faculty::HUMANITIES,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::CIVIL => 'วิศวกรรมโยธา',
            self::MECHANICAL => 'วิศวกรรมเครื่องกล',
            self::ELECTRICAL => 'วิศวกรรมไฟฟ้า',
            self::COMPUTER => 'วิทยาการคอมพิวเตอร์',
            self::BIOLOGY => 'ชีววิทยา',
            self::PHYSICS => 'ฟิสิกส์',
            self::CHEMISTRY => 'เคมี',
            self::ACCOUNTING => 'การบัญชี',
            self::MARKETING => 'การตลาด',
            self::MANAGEMENT => 'การจัดการ',
            self::AGRONOMY => 'พืชไร่นา',
            self::PSYCHOLOGY => 'จิตวิทยา',
            self::LINGUISTICS => 'ภาษาศาสตร์',
        };
    }
}
