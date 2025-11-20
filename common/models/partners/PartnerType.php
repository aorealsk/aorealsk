<?php

namespace common\models\partners;

final class PartnerType
{
    public const ERROR = -1;
    public const SCHOOL = 1;
    public const COMPANY = 2;
    public const DUAL_STUDENT = 4;
    public const DUAL_COMPANY = 3;   // sucast programu dual prax
    public const PRIMARY_SCHOOL = 5;

    public static function getValue(string $type): int
    {
        $partnerList  = [
            'dual_partner' => self::DUAL_COMPANY,
            'dual_skola' => self::SCHOOL,
            'dual_student' => self::DUAL_STUDENT,
        ];
        return $partnerList[$type] ?? self::ERROR;
    }

}
