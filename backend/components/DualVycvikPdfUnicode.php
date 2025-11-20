<?php
namespace backend\components;

use setasign\Fpdi\Tfpdf\Fpdi;

final class DualVycvikPdfUnicode
{
    /** Fallbacks used only when XML doesn't provide coords (23: name; 24: dob/addr). */
    private const COORD_23_NAME = [145, 90];
    private const COORD_24_DOB  = [30,  80];
    private const COORD_24_ADDR = [30,  86];

    /** Page 24 attendance grid geometry (fine-tuned) */
    private const GRID24_X0          = 20.9;   // left edge of day "1" cell
    private const GRID24_Y_SEP       = 145.9;  // Y of the "September" row baseline
    private const GRID24_ROW_H       = 7.05;   // row height (Sep..Jun)
    private const GRID24_COL_W       = 5.10;   // column width (1..31)

    /** Visual centering tweaks for the “X” inside a cell */
    private const GRID24_X_OFFSET    = 22.0;   // move +right / -left
    private const GRID24_Y_OFFSET    = -42.0;  // move +down / -up (baseline -> optical center)

    /** DejaVuSans.ttf must sit next to this PHP file. */
    private static function localFontPath(): ?string
    {
        $p  = __DIR__ . DIRECTORY_SEPARATOR . 'DejaVuSans.ttf';
        $rp = realpath($p);
        return ($rp && is_file($rp) && is_readable($rp)) ? $rp : null;
    }

    /** tFPDF unifont dir (vendor/setasign/tfpdf/font/unifont). */
    private static function unifontDir(): ?string
    {
        try {
            $ref  = new \ReflectionClass(Fpdi::class);
            $base = dirname($ref->getFileName());
            $dir  = dirname($base, 3) . '/setasign/tfpdf/font/unifont';
            return realpath($dir) ?: $dir;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Ensure DejaVuSans.ttf exists in unifont dir. */
    private static function ensureFontInstalled(): ?string
    {
        $src    = self::localFontPath();
        $dstDir = self::unifontDir();
        if (!$src || !$dstDir) return null;

        $dst = rtrim($dstDir, '/\\') . DIRECTORY_SEPARATOR . 'DejaVuSans.ttf';
        if (!is_file($dst)) {
            @mkdir($dstDir, 0775, true);
            @copy($src, $dst);
        }
        return (is_file($dst) && is_readable($dst)) ? 'DejaVuSans.ttf' : null;
    }

    public static function isAvailable(): bool
    {
        return class_exists(Fpdi::class) && (self::ensureFontInstalled() !== null);
    }

    /** Tiny helper: print a single cell at (x,y) with width. */
    private static function printCell(Fpdi $pdf, array $xy, float $w, string $text): void
    {
        $text = trim($text);
        if ($text === '') return;
        [$x,$y] = $xy;
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 5, $text, 0, 0, 'L');
    }

    /** Draw up to 16 attendance rows on page 23 – with column widths & alignment */
    private static function drawShiftsPage23(Fpdi $pdf, array $shifts): void
    {
        if (empty($shifts)) return;

        // aligned to your latest hard-coded geometry
        $X_DATE_L   = 21.0;  $W_DATE   = 17.0;
        $X_FROM_L   = 42.0;  $W_FROM   = 12.0;
        $X_TO_L     = 54.0;  $W_TO     = 12.0;
        $X_HOURS_L  = 66.0;  $W_HOURS  = 12.0;

        $Y_START = 124.0;
        $Y_STEP  = 7.0;
        $MAX     = 16;

        $pdf->SetFont('DejaVu','',9);

        $n = min(count($shifts), $MAX);
        for ($i = 0; $i < $n; $i++) {
            $y = $Y_START + $i * $Y_STEP;

            $d = isset($shifts[$i]['date']) ? (string)$shifts[$i]['date'] : '';
            $d = $d && ($ts = strtotime($d)) ? date('d.m.Y', $ts) : $d;

            $f = self::fmtHm((string)($shifts[$i]['from'] ?? ''));
            $t = self::fmtHm((string)($shifts[$i]['to']   ?? ''));
            $h = (string)($shifts[$i]['hrs'] ?? '');

            // Dátum (L)
            $pdf->SetXY($X_DATE_L, $y);
            $pdf->Cell($W_DATE, 5, $d, 0, 0, 'L');

            // Od / Do / Spolu (C)
            $pdf->SetXY($X_FROM_L, $y);
            $pdf->Cell($W_FROM, 5, $f, 0, 0, 'C');

            $pdf->SetXY($X_TO_L, $y);
            $pdf->Cell($W_TO, 5, $t, 0, 0, 'C');

            $pdf->SetXY($X_HOURS_L, $y);
            $pdf->Cell($W_HOURS, 5, $h, 0, 0, 'C');
        }

        $pdf->SetFont('DejaVu','',10);
    }

    /** NEW: simple bullet list of tasks into "Vykonávaná pracovná činnosť" column on page 23. */
    private static function drawTasksPage23(Fpdi $pdf, array $tasks): void
    {
        if (empty($tasks)) return;

        // Column right of "Spolu hodín", matches non-unicode geometry.
        $X_TASKS_L = 80.0;
        $W_TASKS   = 115.0;
        $Y_START   = 124.0;
        $Y_STEP    = 7.0;
        $MAX       = 16;

        $pdf->SetFont('DejaVu','',9);

        $n = min(count($tasks), $MAX);
        for ($i = 0; $i < $n; $i++) {
            $line = trim((string)$tasks[$i]);
            if ($line === '') continue;
            $y = $Y_START + $i * $Y_STEP;
            $pdf->SetXY($X_TASKS_L, $y);
            $pdf->Cell($W_TASKS, 5, '• '.$line, 0, 0, 'L');
        }

        $pdf->SetFont('DejaVu','',10);
    }

    /** Normalize a time string to HH:MM (no seconds). */
    private static function fmtHm(string $t): string
    {
        $t = trim($t);
        if ($t === '') return '';

        // 08:07:18 or 08:07 or 8:7  → HH:MM
        if (preg_match('/^(\d{1,2})[:\.](\d{1,2})(?::\d{1,2})?$/', $t, $m)) {
            return sprintf('%02d:%02d', (int)$m[1], (int)$m[2]);
        }
        // 0830 or 830 → 08:30
        if (preg_match('/^(\d{1,2})(\d{2})$/', $t, $m)) {
            return sprintf('%02d:%02d', (int)$m[1], (int)$m[2]);
        }

        // Best-effort fallback
        $parts = preg_split('/[:\.]/', $t);
        if (count($parts) >= 2) {
            return sprintf('%02d:%02d', (int)$parts[0], (int)$parts[1]);
        }

        // Last resort: try strtotime
        $ts = strtotime($t);
        if ($ts !== false) return date('H:i', $ts);

        return $t;
    }

    /** Slovak month name from ISO date (YYYY-MM-DD). */
    private static function monthSkFromIso(string $iso): string
    {
        $ts = strtotime($iso ?: 'today');
        $m  = (int)date('n', $ts);
        $sk = [1=>'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'];
        return $sk[$m] ?? '';
    }

    /** Legacy: names only. */
    public static function repeatPagesFromFilledToString(string $inputPdfPath, array $names): string
    {
        $fontFileName = self::ensureFontInstalled();
        if (!$fontFileName) {
            return \backend\components\DualVycvikPdf::repeatPagesFromFilledToString($inputPdfPath, $names);
        }

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->AddFont('DejaVu', '', $fontFileName, true);
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->SetTextColor(0,0,0);

        $src = \Yii::getAlias($inputPdfPath, false) ?: $inputPdfPath;
        $pageCount = $pdf->setSourceFile($src);

        for ($p = 1; $p <= 22 && $p <= $pageCount; $p++) {
            $tpl = $pdf->importPage($p);
            $s   = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
            $pdf->useTemplate($tpl);
        }

        $has23 = $pageCount >= 23;
        $has24 = $pageCount >= 24;

        foreach ($names as $name) {
            if ($has23) {
                $tpl = $pdf->importPage(23);
                $s   = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
                $pdf->useTemplate($tpl);
                self::printCell($pdf, self::COORD_23_NAME, 102, (string)$name);
            }
            if ($has24) {
                $tpl = $pdf->importPage(24);
                $s   = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
                $pdf->useTemplate($tpl);
                self::printCell($pdf, [30, 72], 102, (string)$name);
                self::printCell($pdf, [75, 72], 102, (string)$name);
            }
        }

        for ($p = 25; $p <= $pageCount; $p++) {
            $tpl = $pdf->importPage($p);
            $s   = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
            $pdf->useTemplate($tpl);
        }

        return $pdf->Output('S');
    }

    /**
     * Names + DOB + address + optional first/last + shifts/aggregates (+ tasks).
     * Attendance fields are used on PAGE 23 ONLY (from XML <page ref="23">).
     *
     * @param array<int,array{
     *   name:string, dob:string, address:string,
     *   first_name?:string, last_name?:string,
     *   shifts?:array,
     *   shifts_month?:string,
     *   shifts_total_hours?:string|float|int,
     *   total_hours_in_month?:string|float|int,
     *   shifts_summary?:string,
     *   today?:string,
     *   today_sk?:string,
     *   tasks?:array<int,string>
     * }> $people
     */
    public static function repeatPagesFromFilledToStringWithDetails(string $inputPdfPath, array $people, ?string $xmlPath = null): string
    {
        $fontFileName = self::ensureFontInstalled();
        if (!$fontFileName) {
            return \backend\components\DualVycvikPdf::repeatPagesFromFilledToStringWithDetails($inputPdfPath, $people, $xmlPath);
        }

        // sanitize rows
        $rows = [];
        foreach ($people as $p) {
            if (!is_array($p)) continue;
            $name = isset($p['name']) ? trim((string)$p['name']) : '';
            if ($name === '') continue;

            // contract date defaults if not provided in $people
            $todayIso = isset($p['today']) ? (string)$p['today'] : date('Y-m-d');
            $todaySk  = isset($p['today_sk']) ? (string)$p['today_sk'] : date('d.m.Y');

            $rows[] = [
                'name'                  => $name,
                'dob'                   => isset($p['dob'])        ? trim((string)$p['dob'])        : '',
                'address'               => isset($p['address'])    ? trim((string)$p['address'])    : '',
                'first_name'            => isset($p['first_name']) ? trim((string)$p['first_name']) : '',
                'last_name'             => isset($p['last_name'])  ? trim((string)$p['last_name'])  : '',
                'shifts'                => isset($p['shifts'])     && is_array($p['shifts']) ? $p['shifts'] : [],
                'shifts_month'          => (string)($p['shifts_month']          ?? ''),
                'shifts_total_hours'    => (string)($p['shifts_total_hours']    ?? ''),
                'total_hours_in_month'  => (string)($p['total_hours_in_month']  ?? ''),
                'shifts_summary'        => (string)($p['shifts_summary']        ?? ''),
                'today'                 => $todayIso,
                'today_sk'              => $todaySk,
                'current_month'         => self::monthSkFromIso($todayIso),
                // NEW: tasks for page 23 column
                'tasks'                 => isset($p['tasks']) && is_array($p['tasks']) ? $p['tasks'] : [],
            ];
        }

        $cx = self::readCoordsFromXml($xmlPath);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->AddFont('DejaVu', '', $fontFileName, true);
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->SetTextColor(0,0,0);

        $src = \Yii::getAlias($inputPdfPath, false) ?: $inputPdfPath;
        $pageCount = $pdf->setSourceFile($src);

        for ($p = 1; $p <= 22 && $p <= $pageCount; $p++) {
            $tpl = $pdf->importPage($p);
            $s   = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
            $pdf->useTemplate($tpl);
        }

        $has23 = $pageCount >= 23;
        $has24 = $pageCount >= 24;

        foreach ($rows as $r) {
            if ($has23) {
                $tpl = $pdf->importPage(23);
                $s   = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
                $pdf->useTemplate($tpl);

                // Person name (fallback coords if XML missing)
                [$x,$y,$w] = self::coord($cx, '23', 'name', self::COORD_23_NAME, 102);
                self::printCell($pdf, [$x,$y], $w, $r['name']);

                // Optional person details on page 23 if provided in XML
                if (isset($cx['23']['dob'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'dob', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $r['dob']);
                }
                if (isset($cx['23']['addr'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'addr', null, 140);
                    self::printCell($pdf, [$x,$y], $w, $r['address']);
                }
                if (isset($cx['23']['first'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'first', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $r['first_name']);
                }
                if (isset($cx['23']['last'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'last', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $r['last_name']);
                }

                // Contract date + current month on page 23 (only if coords exist in XML)
                if (isset($cx['23']['today_sk'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'today_sk', null, 35.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['today_sk']);
                }
                if (isset($cx['23']['today'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'today', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['today']);
                }
                if (isset($cx['23']['current_month'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'current_month', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['current_month']);
                }

                // Attendance rows table on page 23
                if (!empty($r['shifts'])) {
                    self::drawShiftsPage23($pdf, $r['shifts']);
                }

                // NEW: tasks in the tasks column on page 23
                if (!empty($r['tasks'])) {
                    self::drawTasksPage23($pdf, (array)$r['tasks']);
                }

                // ----- Attendance header (PAGE 23 ONLY) -----
                $hoursStr = '';
                if ($r['total_hours_in_month'] !== '') {
                    $hoursStr = (string)$r['total_hours_in_month'];
                } elseif ($r['shifts_total_hours'] !== '') {
                    $hoursStr = (string)$r['shifts_total_hours'];
                } elseif (!empty($r['shifts']) && is_array($r['shifts'])) {
                    $sum = 0.0;
                    foreach ($r['shifts'] as $srow) {
                        $sum += (float)($srow['hrs'] ?? 0);
                    }
                    $hoursStr = rtrim(rtrim(number_format($sum, 2, '.', ''), '0'), '.');
                }

                if (isset($cx['23']['shifts_month'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'shifts_month', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['shifts_month']);
                }
                if (isset($cx['23']['shifts_total_hours'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'shifts_total_hours', null, 25.0);
                    self::printCell($pdf, [$x,$y], $w, $hoursStr);
                }
                if (isset($cx['23']['total_hours_in_month'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'total_hours_in_month', null, 25.0);
                    self::printCell($pdf, [$x,$y], $w, $hoursStr);
                }
                if (isset($cx['23']['shifts_summary'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'shifts_summary', null, 160.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['shifts_summary']);
                }
            }

            if ($has24) {
                $tpl = $pdf->importPage(24);
                $s   = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
                $pdf->useTemplate($tpl);

                // Names on 24 if defined in XML
                if (isset($cx['24']['nameA'])) {
                    [$x,$y,$w] = [$cx['24']['nameA'][0], $cx['24']['nameA'][1], $cx['24']['nameA'][2] ?? 102.0];
                    self::printCell($pdf, [$x,$y], $w, $r['name']);
                }
                if (isset($cx['24']['nameB'])) {
                    [$x,$y,$w] = [$cx['24']['nameB'][0], $cx['24']['nameB'][1], $cx['24']['nameB'][2] ?? 102.0];
                    self::printCell($pdf, [$x,$y], $w, $r['name']);
                }

                // DOB + address (with fallbacks if XML missing)
                [$x,$y,$w] = self::coord($cx, '24', 'dob', self::COORD_24_DOB, 102);
                self::printCell($pdf, [$x,$y], $w, $r['dob']);

                [$x,$y,$w] = self::coord($cx, '24', 'addr', self::COORD_24_ADDR, 140);
                self::printCell($pdf, [$x,$y], $w, $r['address']);

                if (isset($cx['24']['first'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'first', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $r['first_name']);
                }
                if (isset($cx['24']['last'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'last', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $r['last_name']);
                }

                // ===== Contract date on page 24 (from XML coords) =====
                if (isset($cx['24']['today_sk'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'today_sk', null, 35.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['today_sk']);
                }
                if (isset($cx['24']['today'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'today', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$r['today']);
                }

                // NEW: attendance X-marks on 24th page grid
                if (!empty($r['shifts'])) {
                    self::drawAttendanceMarksPage24($pdf, $r['shifts']);
                }
            }
        }

        for ($p = 25; $p <= $pageCount; $p++) {
            $tpl = $pdf->importPage($p);
            $s   = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($s['orientation'], [$s['width'], $s['height']]);
            $pdf->useTemplate($tpl);
        }

        return $pdf->Output('S');
    }

    /**
     * Read coordinates from XML:
     *  - Prefer <repeat id="per_person_pages"><page ref="23|24">…</page></repeat>
     *  - Fall back to top-level <field page="23|24" name="…"> if present.
     * Keys are exactly the XML names so moving them in XML moves them in output.
     */
    private static function readCoordsFromXml(?string $xmlPath): array
    {
        $out = ['23'=>[], '24'=>[]];
        if (!$xmlPath || !is_file($xmlPath)) return $out;

        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file($xmlPath);
        if ($xml === false) return $out;

        $grab = static function($page, string $name) {
            $nodes = $page->xpath("text[@name='{$name}']") ?: [];
            if (!$nodes) return null;
            $n = $nodes[0];
            $x = isset($n['x']) ? (float)$n['x'] : null;
            $y = isset($n['y']) ? (float)$n['y'] : null;
            $w = isset($n['width']) ? (float)$n['width'] : null;
            return ($x !== null && $y !== null) ? [$x,$y,$w] : null;
        };

        // 1) from repeat block
        $pages = $xml->xpath("//repeat[@id='per_person_pages']/page") ?: [];
        foreach ($pages as $page) {
            $ref = (string)($page['ref'] ?? '');
            if ($ref !== '23' && $ref !== '24') continue;

            if ($ref === '23') {
                if ($c = $grab($page, 'person_name'))       $out['23']['name']  = $c;
                if ($c = $grab($page, 'person_dob'))        $out['23']['dob']   = $c;
                if ($c = $grab($page, 'person_address'))    $out['23']['addr']  = $c;
                if ($c = $grab($page, 'person_first_name')) $out['23']['first'] = $c;
                if ($c = $grab($page, 'person_last_name'))  $out['23']['last']  = $c;

                // Attendance header fields (page 23)
                if ($c = $grab($page, 'shifts_month'))           $out['23']['shifts_month']         = $c;
                if ($c = $grab($page, 'shifts_total_hours'))     $out['23']['shifts_total_hours']   = $c;
                if ($c = $grab($page, 'total_hours_in_month'))   $out['23']['total_hours_in_month'] = $c;
                if ($c = $grab($page, 'shifts_summary'))         $out['23']['shifts_summary']       = $c;

                // NEW: contract date + month fields (page 23)
                if ($c = $grab($page, 'today'))          $out['23']['today']          = $c;
                if ($c = $grab($page, 'today_sk'))       $out['23']['today_sk']       = $c;
                if ($c = $grab($page, 'current_month'))  $out['23']['current_month']  = $c;

            } else { // 24
                $names = $page->xpath("text[@name='person_name']") ?: [];
                if (isset($names[0])) {
                    $x = (float)($names[0]['x'] ?? 0); $y = (float)($names[0]['y'] ?? 0);
                    $w = isset($names[0]['width']) ? (float)$names[0]['width'] : null;
                    $out['24']['nameA'] = [$x,$y,$w];
                }
                if (isset($names[1])) {
                    $x = (float)($names[1]['x'] ?? 0); $y = (float)($names[1]['y'] ?? 0);
                    $w = isset($names[1]['width']) ? (float)$names[1]['width'] : null;
                    $out['24']['nameB'] = [$x,$y,$w];
                }
                if ($c = $grab($page, 'person_dob'))        $out['24']['dob']   = $c;
                if ($c = $grab($page, 'person_address'))    $out['24']['addr']  = $c;
                if ($c = $grab($page, 'person_first_name')) $out['24']['first'] = $c;
                if ($c = $grab($page, 'person_last_name'))  $out['24']['last']  = $c;

                // NEW: date fields on page 24 (repeat)
                if ($c = $grab($page, 'today'))    $out['24']['today']    = $c;
                if ($c = $grab($page, 'today_sk')) $out['24']['today_sk'] = $c;

                // (No attendance on 24)
            }
        }

        // 2) top-level <field> fallbacks
        $fieldAt = static function(\SimpleXMLElement $xmlDoc, string $name, string $page) {
            $nodes = $xmlDoc->xpath("//field[@name='{$name}' and @page='{$page}']") ?: [];
            if (!$nodes) return null;
            $n = $nodes[0];
            $x = isset($n['x']) ? (float)$n['x'] : null;
            $y = isset($n['y']) ? (float)$n['y'] : null;
            $w = isset($n['width']) ? (float)$n['width'] : null;
            return ($x !== null && $y !== null) ? [$x,$y,$w] : null;
        };

        // attendance (page 23) fallback/override
        foreach (['shifts_month','shifts_total_hours','total_hours_in_month','shifts_summary'] as $nm) {
            if (!isset($out['23'][$nm])) {
                if ($c = $fieldAt($xml, $nm, '23')) { $out['23'][$nm] = $c; }
            }
        }

        // NEW: page 23 fallbacks for date fields + current_month
        if (!isset($out['23']['today']) && ($c = $fieldAt($xml, 'today', '23'))) {
            $out['23']['today'] = $c;
        }
        if (!isset($out['23']['today_sk']) && ($c = $fieldAt($xml, 'today_sk', '23'))) {
            $out['23']['today_sk'] = $c;
        }
        if (!isset($out['23']['current_month']) && ($c = $fieldAt($xml, 'current_month', '23'))) {
            $out['23']['current_month'] = $c;
        }

        // page 24 fallbacks for date fields
        if (!isset($out['24']['today']) && ($c = $fieldAt($xml, 'today', '24'))) {
            $out['24']['today'] = $c;
        }
        if (!isset($out['24']['today_sk']) && ($c = $fieldAt($xml, 'today_sk', '24'))) {
            $out['24']['today_sk'] = $c;
        }

        return $out;
    }

    /**
     * Resolve [x,y,width] from XML coords or a fallback point.
     * @param string $key one of:
     *  name|nameA|nameB|dob|addr|first|last|shifts_month|shifts_total_hours|total_hours_in_month|shifts_summary|today|today_sk|current_month
     */
    private static function coord(array $cx, string $page, string $key, ?array $fallbackXY, float $fallbackW): array
    {
        if (isset($cx[$page][$key]) && is_array($cx[$page][$key])) {
            $x = (float)$cx[$page][$key][0];
            $y = (float)$cx[$page][$key][1];
            $w = isset($cx[$page][$key][2]) && $cx[$page][$key][2] !== null ? (float)$cx[$page][$key][2] : $fallbackW;
            return [$x,$y,$w];
        }
        if ($fallbackXY !== null) {
            $x = $fallbackXY[0] ?? 0.0;
            $y = $fallbackXY[1] ?? 0.0;
            return [$x,$y,$fallbackW];
        }
        return [0.0, 0.0, $fallbackW];
    }

    /** Draw X marks into the 24th-page month/day grid from $shifts[] (date, from, to, hrs). */
    private static function drawAttendanceMarksPage24(Fpdi $pdf, array $shifts): void
    {
        if (empty($shifts)) return;

        // map month -> row index (Sep..Jun = 10 rows)
        $rowMap = [9=>0,10=>1,11=>2,12=>3,1=>4,2=>5,3=>6,4=>7,5=>8,6=>9];

        // collect unique (row,col) to mark
        $marks = [];
        foreach ($shifts as $s) {
            $d = (string)($s['date'] ?? '');
            if ($d === '' || ($ts = strtotime($d)) === false) continue;

            $m   = (int)date('n', $ts);
            $day = (int)date('j', $ts);
            if (!isset($rowMap[$m]) || $day < 1 || $day > 31) continue;

            $r = $rowMap[$m];
            $c = $day - 1; // 0-based col index
            $marks[$r][$c] = true;
        }
        if (!$marks) return;

        // Slightly smaller font for better centering inside the cells
        $pdf->SetFont('DejaVu','',8.5);

        foreach ($marks as $r => $cols) {
            // base Y for this month row + visual offset
            $y = self::GRID24_Y_SEP + $r * self::GRID24_ROW_H + self::GRID24_Y_OFFSET;

            foreach (array_keys($cols) as $c) {
                // base X for this day column + visual offset
                $x = self::GRID24_X0 + $c * self::GRID24_COL_W + self::GRID24_X_OFFSET;

                // center an "X" in the cell
                $pdf->SetXY($x, $y);
                $pdf->Cell(self::GRID24_COL_W, self::GRID24_ROW_H, 'X', 0, 0, 'C');
            }
        }

        $pdf->SetFont('DejaVu','',10);
    }

}
