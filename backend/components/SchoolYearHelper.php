<?php
namespace backend\components;

use DateTimeImmutable;
use DateTimeZone;

final class SchoolYearHelper
{
    /**
     * Szeptember 1. előtt: (Y-1)/Y, különben: Y/(Y+1)
     * Pl. 2025.08.31 → 2024/2025, 2025.09.01 → 2025/2026
     */
    public static function current(?DateTimeImmutable $now = null, ?DateTimeZone $tz = null): string
    {
        $tz   = $tz ?: new DateTimeZone('Europe/Bratislava');
        $now  = $now ?: new DateTimeImmutable('now', $tz);
        $y    = (int)$now->format('Y');
        $m    = (int)$now->format('n');
        return ($m < 9) ? ($y - 1) . '/' . $y : $y . '/' . ($y + 1);
    }
}
