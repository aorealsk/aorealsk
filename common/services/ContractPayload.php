<?php
namespace backend\services\contracts;

use common\models\User;
use common\models\UserGuardian;
use DateTimeImmutable;

class ContractPayload
{
    private string $schoolYear;

    public function __construct(string $schoolYear = '')
    {
        $this->schoolYear = $schoolYear;
    }

    public function build(User $u): array
    {
        // Normalize birthdate & derive age
        [$birthRaw, $birthSk, $age, $isMinor] = $this->normalizeBirthday($u->birthdate);

        // Full address
        $fullAddr = $this->joinParts([$u->street, $u->street_no, $u->zip, $u->city]);

        // Guardians (max 2)
        $g = UserGuardian::find()
                ->where(['user_id' => $u->id])
                ->orderBy(['id' => SORT_ASC])
                ->limit(2)
                ->all();

        $g1 = $g[0] ?? null;
        $g2 = $g[1] ?? null;

        $out = [
            // User
            'user_id'       => (int)$u->id,
            'username'      => (string)$u->username,
            'name_first'    => (string)$u->name_first,
            'name_last'     => (string)$u->name_last,
            'full_name'     => trim(($u->name_first ?? '') . ' ' . ($u->name_last ?? '')),
            'birthdate'     => $birthRaw,                // YYYY-MM-DD
            'birthdate_sk'  => $birthSk,                 // DD.MM.YYYY
            'age'           => $age,
            'is_minor'      => $isMinor ? 1 : 0,

            'street'        => (string)$u->street,
            'street_no'     => (string)$u->street_no,
            'zip'           => (string)$u->zip,
            'city'          => (string)$u->city,
            'full_address'  => $fullAddr,

            'phone'         => (string)$u->phone,
            'email'         => (string)$u->email,
            'iban'          => (string)$u->iban,

            // Dates
            'today'         => (new DateTimeImmutable('today'))->format('Y-m-d'),
            'today_sk'      => (new DateTimeImmutable('today'))->format('d.m.Y'),
            'school_year'   => $this->schoolYear,

            // Guardian #1
            'g1_name'       => $g1->name      ?? '',
            'g1_relation'   => $g1->relation  ?? '',
            'g1_phone'      => $g1->phone     ?? '',
            'g1_email'      => $g1->email     ?? '',
            'g1_street'     => $g1->street    ?? '',
            'g1_street_no'  => $g1->street_no ?? '',
            'g1_zip'        => $g1->zip       ?? '',
            'g1_city'       => $g1->city      ?? '',
            'g1_full_address'=> $this->guardianAddress($g1),

            // Guardian #2
            'g2_name'       => $g2->name      ?? '',
            'g2_relation'   => $g2->relation  ?? '',
            'g2_phone'      => $g2->phone     ?? '',
            'g2_email'      => $g2->email     ?? '',
            'g2_street'     => $g2->street    ?? '',
            'g2_street_no'  => $g2->street_no ?? '',
            'g2_zip'        => $g2->zip       ?? '',
            'g2_city'       => $g2->city      ?? '',
            'g2_full_address'=> $this->guardianAddress($g2),
        ];

        return $out;
    }

    /** Helpers */

    private function joinParts(array $parts): string
    {
        $p = array_filter(array_map('trim', $parts), fn($v) => $v !== '' && $v !== null);
        if (empty($p)) { return ''; }

        $street   = $p[0] ?? '';
        $streetNo = $p[1] ?? '';
        $cityZip  = trim(($p[2] ?? '') . ' ' . ($p[3] ?? ''));

        $left  = trim($street . ($streetNo ? ' ' . $streetNo : ''));
        $right = $cityZip;
        return trim($left . ($right ? ', ' . $right : ''));
    }

    private function guardianAddress($g): string
    {
        if (!$g) return '';
        return $this->joinParts([$g->street, $g->street_no, $g->zip, $g->city]);
    }

    /**
     * @return array [Y-m-d, d.m.Y, age, isMinor]
     */
    private function normalizeBirthday(?string $raw): array
    {
        if (!$raw) { return ['', '', null, false]; }

        $dt = $this->parseAnyDate($raw);
        if (!$dt) { return [$raw, $raw, null, false]; }

        $yMd = $dt->format('Y-m-d');
        $sk  = $dt->format('d.m.Y');

        $today = new DateTimeImmutable('today');
        $age   = (int)$today->diff($dt)->y;
        return [$yMd, $sk, $age, $age < 18];
    }

    private function parseAnyDate(string $s): ?DateTimeImmutable
    {
        $s = trim($s);
        foreach (['Y-m-d','d.m.Y','d/m/Y','m/d/Y'] as $fmt) {
            $dt = \DateTimeImmutable::createFromFormat($fmt, $s);
            if ($dt && $dt->format($fmt) === $s) { return $dt; }
        }
        $ts = strtotime($s);
        return $ts ? (new DateTimeImmutable())->setTimestamp($ts) : null;
    }
}
