<?php
namespace common\services;

use Yii;
use common\models\User;
use common\models\Partner;
use common\models\UserAttendance;
use common\models\StudyPlanItem;
use common\models\CalendarEvent;
use common\models\forms\ContractBatchForm;
use common\models\StudyPlanType;

class ContractGenerator
{
    private ContractBatchForm $form;

    public function __construct(ContractBatchForm $form)
    {
        $this->form = $form;
        @date_default_timezone_set('Europe/Bratislava');
    }

    public function buildContractsData(): array
    {
        if (empty($this->form->userIds) || !is_array($this->form->userIds)) {
            return [];
        }

        $users = User::find()
            ->where(['id' => $this->form->userIds])
            ->orderBy(['name_last' => SORT_ASC, 'name_first' => SORT_ASC])
            ->all();
        if (!$users) {
            return [];
        }

        $supervisor = User::findOne($this->form->supervisorId);
        $partner    = Partner::findOne($this->form->partnerId);

        // ---- Page 1: Attendance on the chosen date ----
        $attendanceByUser = [];
        $atts = UserAttendance::find()
            ->where([
                'uaDate' => $this->form->shiftDate,
                'userId' => array_map(static fn($u) => $u->id, $users),
            ])->all();
        foreach ($atts as $a) {
            $attendanceByUser[$a->userId][] = $a;
        }

        // ---- Study plan items ----
        $itemsByType = [];
        $items = StudyPlanItem::find()
            ->where(['month' => $this->form->studyPlanMonth])
            ->orderBy(['position' => SORT_ASC])
            ->all();
        foreach ($items as $it) {
            $itemsByType[$it->type_id][] = $it;
        }

        // ---- Odbor štúdia mapping ----
        $typeIds = array_values(array_unique(array_filter(array_map(
            static fn($u) => $u->study_plan_type_id ?? null, $users
        ))));
        $typeMap = [];
        if ($typeIds) {
            foreach (StudyPlanType::find()->where(['id' => $typeIds])->all() as $t) {
                $typeMap[$t->id] = $t->name;
            }
        }

        // ------------ Month selection ------------
        $selYm  = $this->form->studyPlanMonth ?: date('Y-m'); // 'YYYY-MM'
        $selY   = (int)substr($selYm, 0, 4);
        $selM   = (int)substr($selYm, 5, 2);

        // School year starts in September
        $schoolStartY = ($selM >= 9) ? $selY : ($selY - 1);

        // Build months list from September -> selected month (inclusive)
        $monthsPlan = [];
        for ($m = 9; $m <= 12; $m++) {
            $ym = sprintf('%04d-%02d', $schoolStartY, $m);
            if ($ym <= $selYm) {
                $monthsPlan[] = [
                    'ym'    => $ym,
                    'label' => $this->skMonthLabel($m),
                    'days'  => (int)date('t', strtotime("$ym-01")),
                ];
            }
        }
        for ($m = 1; $m <= 6; $m++) {
            $ym = sprintf('%04d-%02d', $schoolStartY + 1, $m);
            if ($ym <= $selYm) {
                $monthsPlan[] = [
                    'ym'    => $ym,
                    'label' => $this->skMonthLabel($m),
                    'days'  => (int)date('t', strtotime("$ym-01")),
                ];
            }
        }
        if (empty($monthsPlan)) {
            // Fallback – at least the selected month
            $monthsPlan[] = [
                'ym'    => $selYm,
                'label' => $this->skMonthLabel($selM),
                'days'  => (int)date('t', strtotime("$selYm-01")),
            ];
        }

        // Pull ALL events within the overall range (Sep -> selected)
        $rangeStart = $monthsPlan[0]['ym'] . '-01 00:00:00';
        $lastYm     = end($monthsPlan)['ym'];
        $rangeEnd   = date('Y-m-t 23:59:59', strtotime("$lastYm-01"));

        $events = CalendarEvent::find()
            ->where(['between', 'start', $rangeStart, $rangeEnd])
            ->andWhere(['user_id' => array_map(static fn($u) => $u->id, $users)])
            ->orderBy(['start' => SORT_ASC])
            ->all();

        // Group events by user + month
        $eventsByUserMonth = []; // [uid][YYYY-MM][] = row
        foreach ($events as $ev) {
            $sTs = $this->ts($ev->start);
            $eTs = $this->ts($ev->end);
            if ($sTs <= 0 || $eTs <= 0 || $eTs <= $sTs) continue;

            $type  = $this->normType($ev->type ?? '');
            if ($type === '') {
                // if type is not explicitly set, try infer from title
                $type = $this->inferTypeFromTitle((string)($ev->title ?? ''));
            }

            $ym = date('Y-m', $sTs);
            $eventsByUserMonth[$ev->user_id][$ym][] = [
                'date'  => date('d.m.Y', $sTs),
                'from'  => date('H:i',   $sTs),
                'to'    => date('H:i',   $eTs),
                'title' => (string)($ev->title ?? ''),
                'hours' => $this->hoursBetween($sTs, $eTs),
                'type'  => $type,
                '_s'    => $sTs,
            ];
        }

        // ---- assemble payloads per-user ----
        $contracts = [];
        foreach ($users as $user) {

            // Page 2 (keep as-is: selected month rows + simple total)
            $rowsP2 = $eventsByUserMonth[$user->id][$selYm] ?? [];
            $eventsForPage2 = [];
            $hoursTotal = 0.0;
            foreach ($rowsP2 as $r) {
                $eventsForPage2[] = [
                    'date'  => $r['date'],
                    'from'  => $r['from'],
                    'to'    => $r['to'],
                    'title' => $r['title'],
                    'hours' => (float)$r['hours'],
                    'type'  => $r['type'],
                ];
                $hoursTotal += (float)$r['hours'];
            }
            $hoursTotal = round($hoursTotal, 2);

            // Page 3: all months Sep -> selected
            $gridAllMonths = []; // each: ['label','days','values'=> 1..31 => 'A#' | '#']
            foreach ($monthsPlan as $mp) {
                $ym   = $mp['ym'];
                $days = (int)$mp['days'];

                $absByDay = array_fill(1, 31, 0.0); // doctor/sick/holiday/other
                $wrkByDay = array_fill(1, 31, 0.0); // shift / work-like

                foreach (($eventsByUserMonth[$user->id][$ym] ?? []) as $r) {
                    $d = (int)date('j', $r['_s']); if ($d < 1 || $d > 31) continue;
                    $type = $this->normType($r['type'] ?? '');
                    $h    = (float)$r['hours'];

                    if ($this->isAbsenceType($type)) {
                        $absByDay[$d] += $h;
                    } elseif ($type === 'shift' || $this->isWorkday($this->norm($r['title'] ?? ''))) {
                        $wrkByDay[$d] += $h;
                    } else {
                        // unknown → treat as work hours
                        $wrkByDay[$d] += $h;
                    }
                }

                $vals = array_fill(1, 31, '');
                for ($d=1; $d<= $days; $d++) {
                    if ($absByDay[$d] > 0) {
                        $vals[$d] = 'A' . $this->fmtHoursIntCeil($absByDay[$d]);
                    } elseif ($wrkByDay[$d] > 0) {
                        $vals[$d] = (string)$this->fmtHoursIntCeil($wrkByDay[$d]);
                    }
                }

                $gridAllMonths[] = [
                    'label'  => $mp['label'],
                    'days'   => $days,
                    'values' => $vals,
                ];
            }

            // Legacy single-month fields (keep compatibility)
            $legacyDays = (int)date('t', strtotime("$selYm-01"));
            $legacyAbs  = array_fill(1, 31, 0.0);
            $legacyWrk  = array_fill(1, 31, 0.0);
            foreach (($eventsByUserMonth[$user->id][$selYm] ?? []) as $r) {
                $d = (int)date('j', $r['_s']);
                $type = $this->normType($r['type'] ?? '');
                $h    = (float)$r['hours'];

                if ($this->isAbsenceType($type)) $legacyAbs[$d] += $h;
                elseif ($type==='shift' || $this->isWorkday($this->norm($r['title'] ?? ''))) $legacyWrk[$d] += $h;
                else $legacyWrk[$d] += $h;
            }
            $legacyVals = array_fill(1, 31, '');
            for ($d=1; $d<= $legacyDays; $d++) {
                if ($legacyAbs[$d] > 0) $legacyVals[$d] = 'A'.$this->fmtHoursIntCeil($legacyAbs[$d]);
                elseif ($legacyWrk[$d] > 0) $legacyVals[$d] = (string)$this->fmtHoursIntCeil($legacyWrk[$d]);
            }
            $legacyLabel = $this->skMonthLabel($selM);

            // Page 1 helper – keep excused as doctor/sick/other (holiday excluded as before)
            $excusedSum = 0.0;
            foreach (($eventsByUserMonth[$user->id][$selYm] ?? []) as $r) {
                if ($this->isExcusedForPage1($this->normType($r['type'] ?? ''))) {
                    $excusedSum += (float)$r['hours'];
                }
            }

            $contracts[] = [
                'user'               => $user,
                'supervisor'         => $supervisor,
                'partner'            => $partner,
                'company'            => $this->form->companyName ?? '',
                'shiftDate'          => $this->form->shiftDate ?? '',
                'studyMonth'         => $this->form->studyPlanMonth ?? '',
                'attendance'         => $attendanceByUser[$user->id] ?? [],
                'studyPlan'          => $itemsByType[$user->study_plan_type_id] ?? [],
                'batchUsers'         => $users,

                // Page 2:
                'events'             => $eventsForPage2,
                'hoursTotal'         => $hoursTotal,

                // Page 3 (multi-month):
                // Use this in a multi-month-aware _page3.php (iterate gridAllMonths)
                'gridAllMonths'      => $gridAllMonths,
                'studyPlanTypeName'  => $typeMap[$user->study_plan_type_id] ?? '',

                // Legacy single-month (keeps older _page3.php working):
                'gridMonthLabel'     => $legacyLabel,
                'gridValues'         => $legacyVals,
                'gridDaysInMonth'    => $legacyDays,

                // Page 1: excused total (ceil)
                'excusedHoursInt'    => $this->fmtHoursIntCeil($excusedSum),
            ];
        }

        return $contracts;
    }

    public function renderBundleHeaderCss(): string
    {
        return <<<HTML
<style>
  .page-break { page-break-after: always; }

  /* Slightly smaller text everywhere on the pages */
  .abs-page, .p2-wrap, .p3-wrap { font-size: 8.25pt; line-height: 1.15; }

  /* Tighter cell padding */
  .abs-table th, .abs-table td,
  .p2-grid   th, .p2-grid   td,
  .p3-grid   th, .p3-grid   td { padding: 3px 4px; }

  .p2-box td, .p3-box td, .abs-box td { padding: 4px 5px; height: auto; }

  /* Fallback grid */
  .grid th, .grid td { padding: 3px 4px; }
</style>
HTML;
    }

    // ---------- helpers ----------

    /** Normalize: lowercase + trim + collapse whitespace. */
    private function norm(string $s): string
    {
        $s = mb_strtolower($s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    /** Normalize event type (lowercase, single word). */
    private function normType(?string $t): string
    {
        if ($t === null) return '';
        return trim(mb_strtolower($t));
    }

    /** Absence for Page 3 "A#" rendering. */
    private function isAbsenceType(string $type): bool
    {
        // treat these as absences: A#
        return in_array($type, ['doctor','sick','holiday','other'], true);
    }

    /** Excused for Page 1 counter — keep your original behavior (holiday NOT counted if you prefer). */
    private function isExcusedForPage1(string $type): bool
    {
        return in_array($type, ['doctor','sick','other'], true);
    }

    /** Title-based inference when type is empty. */
    private function inferTypeFromTitle(string $title): string
    {
        $t = $this->norm($title);
        if ($t === '') return '';
        // doctor-ish
        if (preg_match('/\b(dr\.?|doctor|dokt(?:or|orka)|lek[aá]r|ambulanci|poliklinik|ordin[aá]ci|vy[šs]etren|kontrol|zub[aá]r|stomatolog|orvos|rendel|vizsg[aá]lat)\b/u', $t)) {
            return 'doctor';
        }
        // holiday-ish
        if (preg_match('/\b(holiday|dovolen|pr[aá]zdnin|vo[lľ]no)\b/u', $t)) {
            return 'holiday';
        }
        // sick-ish
        if (preg_match('/\b(sick|chor|pn)\b/u', $t)) {
            return 'sick';
        }
        // shift/work-ish
        if ($this->isWorkday($t)) return 'shift';
        return '';
    }

    /** Work-like titles (fallback). */
    private function isWorkday(string $titleNorm): bool
    {
        // matches: workday, work, shift, smena, pracovný/pracovny, práca/praca, brigáda, pracovisko, výkon práce
        $re = '/\b(workday|work|shift|smena|pracovn[ýy]|pr[aá]ca|brig[aá]d|pracovisk|v[yý]kon pr[aá]ce)\b/u';
        return (bool)preg_match($re, $titleNorm);
    }

    /** Safe strtotime wrapper returning 0 when invalid. */
    private function ts(?string $dt): int
    {
        if (!$dt) return 0;
        $t = strtotime($dt);
        return $t ?: 0;
    }

    /** Hours between two timestamps (>=0), rounded to 2 decimals. */
    private function hoursBetween(int $startTs, int $endTs): float
    {
        if ($startTs <= 0 || $endTs <= 0 || $endTs <= $startTs) {
            return 0.0;
        }
        return round(($endTs - $startTs) / 3600, 2);
    }

    /** Format hours as integer rounded UP (for Page 3 cells and excused total). */
    private function fmtHoursIntCeil(float $h): int
    {
        if ($h <= 0) return 0;
        return (int)ceil($h);
    }

    /** Month label in SK used in your tables. */
    private function skMonthLabel(int $m): string
    {
        $map = [
            1=>'Január', 2=>'Február', 3=>'Marec', 4=>'Apríl', 5=>'Máj', 6=>'Jún',
            7=>'Júl', 8=>'August', 9=>'September', 10=>'Október', 11=>'November', 12=>'December'
        ];
        return $map[$m] ?? date('F', mktime(0,0,0,$m,1));
    }
}
