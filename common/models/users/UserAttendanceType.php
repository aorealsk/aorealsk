<?php

namespace common\models\users;

final class UserAttendanceType
{
    public const REGULAR_WORKTIME = 1; // regularny pracovny cas
    public const SICKNESS_ABSENCE = 2; // PN
    public const DOCTOR_VISIT = 3;
    public const UNVERIFIED_ABSENCE = 4;
    public const ABSENCE = 5;
    public const UNSOLVED_ABSENCE = 6;
    public const VACATION = 7;
}
