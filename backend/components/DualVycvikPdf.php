<?php
namespace backend\components;

use setasign\Fpdi\Fpdi;

class DualVycvikPdf
{
    /** 22. oldal sorainak koordinátái (max 17 név) */
    private const COORDS_22 = [
        [30,84],[30,94],[30,104],[30,114],[30,125],[30,135],[30,145],[30,156],[30,166],
        [30,176],[30,186],[30,197],[30,207],[30,217],[30,228],[30,238],[30,248],
    ];

    /** 23–24. oldali névpozíciók (fallback) */
    private const COORD_23   = [145,90];
    private const COORD_24_A = [30,72];
    private const COORD_24_B = [75,72];

    /** Fallback koordináták DOB/ADDR-hoz (24-en marad fallback, 23-on csak XML esetén írunk) */
    private const COORD_23_DOB   = [145, 96];
    private const COORD_23_ADDR  = [145, 102];
    private const COORD_24_DOB   = [30,  80];
    private const COORD_24_ADDR  = [30,  86];

    /** Page 24 attendance grid geometry (fine-tuned) */
    private const GRID24_X0          = 20.9;   // left edge of day "1" cell
    private const GRID24_Y_SEP       = 145.9;  // Y of the "September" row baseline
    private const GRID24_ROW_H       = 7.05;   // row height (Sep..Jun)
    private const GRID24_COL_W       = 5.10;   // column width (1..31)

    /** Visual centering tweaks for the “X” inside a cell */
    private const GRID24_X_OFFSET    = 22.0;    // move +right / -left
    private const GRID24_Y_OFFSET    = -42.0;   // move +down / -up (baseline -> optical center)


    /**
     * UTÓFELDOLGOZÁS STRINGBE (csak nevekkel – régi viselkedés).
     */
    public static function repeatPagesFromFilledToString(string $inputPdfPath, array $names): string
    {
        $names = self::sanitizeNames($names);

        $pdf = new FPDI();
        $pdf->SetAutoPageBreak(false);
        $pageCount = $pdf->setSourceFile($inputPdfPath);

        // 1–22. oldalak
        for ($p = 1; $p <= 22 && $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        // Névenként 23–24.
        $has23 = $pageCount >= 23;
        $has24 = $pageCount >= 24;

        foreach ($names as $name) {
            if ($has23) {
                self::importPage($pdf, 23);
                self::printCell($pdf, self::COORD_23, 102, $name);
            }
            if ($has24) {
                self::importPage($pdf, 24);
                self::printCell($pdf, self::COORD_24_A, 102, $name);
                self::printCell($pdf, self::COORD_24_B, 102, $name);
            }
        }

        // 25. → végéig
        for ($p = 25; $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        return $pdf->Output('S');
    }

    /**
     * ÚJ: UTÓFELDOLGOZÁS STRINGBE NÉV + DOB + CÍM (+ opcionális first/last) + (opcionális shifts/aggr) MEZŐKKEL
     * Ha $xmlPath meg van adva és tartalmaz <repeat id="per_person_pages"> pozíciókat,
     * azokat használjuk. Külön szabály: a 23. oldalon DOB/ADDR CSAK akkor íródik, ha az XML megadja.
     * A 24. oldalon a nevek CSAK XML esetén íródnak ki (nameA/nameB).
     */
    public static function repeatPagesFromFilledToStringWithDetails(string $inputPdfPath, array $people, ?string $xmlPath = null): string
    {
        // sorok tisztítása
        $rows = [];
        foreach ($people as $p) {
            if (!is_array($p)) { continue; }
            $name = isset($p['name']) ? trim((string)$p['name']) : '';
            if ($name === '') { continue; }

            // biztosítsuk a dátumokat (ha nincs a $people-ben, használjuk a mai napot)
            $todayIso = isset($p['today']) ? (string)$p['today'] : date('Y-m-d');
            $todaySk  = isset($p['today_sk']) ? (string)$p['today_sk'] : date('d.m.Y');

            $rows[] = [
                'name'       => $name,
                'dob'        => isset($p['dob'])        ? trim((string)$p['dob'])        : '',
                'address'    => isset($p['address'])    ? trim((string)$p['address'])    : '',
                'first_name' => isset($p['first_name']) ? trim((string)$p['first_name']) : '',
                'last_name'  => isset($p['last_name'])  ? trim((string)$p['last_name'])  : '',
                'shifts'     => isset($p['shifts']) && is_array($p['shifts']) ? $p['shifts'] : [],
                'shifts_month'         => isset($p['shifts_month'])         ? (string)$p['shifts_month']         : '',
                'shifts_total_hours'   => isset($p['shifts_total_hours'])   ? (string)$p['shifts_total_hours']   : '',
                'total_hours_in_month' => isset($p['total_hours_in_month']) ? (string)$p['total_hours_in_month'] : '',
                'shifts_summary'       => isset($p['shifts_summary'])       ? (string)$p['shifts_summary']       : '',
                'today'                => $todayIso,
                'today_sk'             => $todaySk,
                'current_month'        => self::monthSkFromIso($todayIso),
                // ÚJ: havi feladatlista a 23. oldal "Vykonávaná pracovná činnosť" oszlophoz
                'tasks'                => isset($p['tasks']) && is_array($p['tasks']) ? $p['tasks'] : [],
            ];
        }

        // XML koordináták (ha vannak)
        $cx = self::readCoordsFromXml($xmlPath);

        $pdf = new FPDI();
        $pdf->SetAutoPageBreak(false);
        $pageCount = $pdf->setSourceFile($inputPdfPath);

        // 1–22 változatlan
        for ($p = 1; $p <= 22 && $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        $has23 = $pageCount >= 23;
        $has24 = $pageCount >= 24;

        foreach ($rows as $row) {
            $name = $row['name'];
            $dob  = $row['dob'];
            $addr = $row['address'];
            $fn   = $row['first_name'];
            $ln   = $row['last_name'];
            $sh   = $row['shifts'];
            $mth  = $row['shifts_month'];
            $tot  = $row['shifts_total_hours'];
            $sum  = $row['shifts_summary'];
            $totMonth = $row['total_hours_in_month'];

            // --- 23. oldal
            if ($has23) {
                self::importPage($pdf, 23);

                // Név (23-on mindig van fallback)
                [$x,$y,$w] = self::coord($cx, '23', 'name', self::COORD_23, 102);
                self::printCell($pdf, [$x,$y], $w, $name);

                // DOB + Cím CSAK ha az XML megadja a pozíciókat
                if (isset($cx['23']['dob'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'dob', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $dob);
                }
                if (isset($cx['23']['addr'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'addr', null, 140);
                    self::printCell($pdf, [$x,$y], $w, $addr);
                }

                // Opcionális keresztnév/vezetéknév – csak ha XML-ben megadva
                if (isset($cx['23']['first'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'first', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $fn);
                }
                if (isset($cx['23']['last'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'last', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $ln);
                }

                // Szerződés dátuma + hónap (ha van XML koordináta)
                if (isset($cx['23']['today_sk'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'today_sk', null, 35.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$row['today_sk']);
                }
                if (isset($cx['23']['today'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'today', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$row['today']);
                }
                if (isset($cx['23']['current_month'])) {
                    [$x,$y,$w] = self::coord($cx, '23', 'current_month', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, (string)$row['current_month']);
                }

                // Dochádzka a 23. oldal táblázatába
                if (!empty($sh)) {
                    self::drawShiftsPage23($pdf, $sh);
                }

                // >>> ÚJ: feladatok a "Vykonávaná pracovná činnosť" oszlopba (egyszerű lista)
                if (!empty($row['tasks'])) {
                    self::drawTasksPage23($pdf, (array)$row['tasks']);
                }
                // <<<

                // ====== Aggregátumok / fejléc a 23. oldalon ======
                $hoursStr = '';
                if ($totMonth !== '') {
                    $hoursStr = (string)$totMonth;
                } elseif ($tot !== '') {
                    $hoursStr = (string)$tot;
                } elseif (!empty($sh)) {
                    $sumH = 0.0;
                    foreach ($sh as $srow) { $sumH += (float)($srow['hrs'] ?? 0); }
                    $hoursStr = rtrim(rtrim(number_format($sumH, 2, '.', ''), '0'), '.');
                }

                if (isset($cx['23']['shifts_month']) && $mth !== '') {
                    [$x,$y,$w] = self::coord($cx, '23', 'shifts_month', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, $mth);
                }
                if ($hoursStr !== '') {
                    if (isset($cx['23']['total_hours_in_month'])) {
                        [$x,$y,$w] = self::coord($cx, '23', 'total_hours_in_month', null, 28.0);
                        self::printCell($pdf, [$x,$y], $w, $hoursStr);
                    }
                    if (isset($cx['23']['shifts_total_hours'])) {
                        [$x,$y,$w] = self::coord($cx, '23', 'shifts_total_hours', null, 25.0);
                        self::printCell($pdf, [$x,$y], $w, $hoursStr);
                    }
                }
                if (isset($cx['23']['shifts_summary']) && $sum !== '') {
                    [$x,$y,$w] = self::coord($cx, '23', 'shifts_summary', null, 160.0);
                    self::printCell($pdf, [$x,$y], $w, $sum);
                }
            }

            // --- 24. oldal
            if ($has24) {
                self::importPage($pdf, 24);

                // Nevek a 24-en CSAK XML esetén
                if (isset($cx['24']['nameA'])) {
                    $x = (float)$cx['24']['nameA'][0];
                    $y = (float)$cx['24']['nameA'][1];
                    $w = isset($cx['24']['nameA'][2]) && $cx['24']['nameA'][2] !== null ? (float)$cx['24']['nameA'][2] : 102.0;
                    self::printCell($pdf, [$x,$y], $w, $name);
                }
                if (isset($cx['24']['nameB'])) {
                    $x = (float)$cx['24']['nameB'][0];
                    $y = (float)$cx['24']['nameB'][1];
                    $w = isset($cx['24']['nameB'][2]) && $cx['24']['nameB'][2] !== null ? (float)$cx['24']['nameB'][2] : 102.0;
                    self::printCell($pdf, [$x,$y], $w, $name);
                }

                // DOB + Cím (24-en fallbackkal)
                [$x,$y,$w] = self::coord($cx, '24', 'dob', self::COORD_24_DOB, 102);
                self::printCell($pdf, [$x,$y], $w, $dob);

                [$x,$y,$w] = self::coord($cx, '24', 'addr', self::COORD_24_ADDR, 140);
                self::printCell($pdf, [$x,$y], $w, $addr);

                // Opcionális keresztnév/vezetéknév
                if (isset($cx['24']['first'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'first', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $fn);
                }
                if (isset($cx['24']['last'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'last', null, 102);
                    self::printCell($pdf, [$x,$y], $w, $ln);
                }

                // Szerződés dátuma a 24. oldalon
                if (isset($cx['24']['today_sk'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'today_sk', null, 35.0);
                    self::printCell($pdf, [$x,$y], $w, $row['today_sk']);
                }
                if (isset($cx['24']['today'])) {
                    [$x,$y,$w] = self::coord($cx, '24', 'today', null, 40.0);
                    self::printCell($pdf, [$x,$y], $w, $row['today']);
                }

                // >>> ÚJ: hivatalos jelenlét – X jelölések a 24. oldal rácsán
                if (!empty($sh)) {
                    self::drawAttendanceMarksPage24($pdf, $sh);
                }
                // <<<
            }
        }

        // 25. → végéig
        for ($p = 25; $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        return $pdf->Output('S');
    }

    /**
     * (OPCIONÁLIS) Teljes generálás a base_form-ból, visszaadott STRINGBE.
     */
    public static function generateFromBaseToString(string $basePdfPath, array $names): string
    {
        $names = self::sanitizeNames($names);

        $pdf = new FPDI();
        $pdf->SetAutoPageBreak(false);
        $pageCount = $pdf->setSourceFile($basePdfPath);

        // 1–21.
        for ($p = 1; $p <= 21 && $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        // 22. + max 17 név
        if ($pageCount >= 22) {
            self::importPage($pdf, 22);
            foreach (array_slice($names, 0, 17) as $i => $name) {
                self::printCell($pdf, self::COORDS_22[$i], 102, $name);
            }
        }

        // 23–24 névenként
        $has23 = $pageCount >= 23;
        $has24 = $pageCount >= 24;

        foreach ($names as $name) {
            if ($has23) {
                self::importPage($pdf, 23);
                self::printCell($pdf, self::COORD_23, 102, $name);
            }
            if ($has24) {
                self::importPage($pdf, 24);
                self::printCell($pdf, self::COORD_24_A, 102, $name);
                self::printCell($pdf, self::COORD_24_B, 102, $name);
            }
        }

        // 25. → végéig
        for ($p = 25; $p <= $pageCount; $p++) {
            self::importPage($pdf, $p);
        }

        return $pdf->Output('S');
    }

    /** VISSZAFELÉ KOMPATIBILIS WRAPPER (fájlba ír). */
    public static function generate(string $basePdfPath, array $names, string $outPath): void
    {
        $binary = self::generateFromBaseToString($basePdfPath, $names);
        file_put_contents($outPath, $binary);
    }

    /** Közvetlen letöltés küldése Response-on keresztül. */
    public static function streamPdf(string $binary, string $downloadName = 'document.pdf'): void
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type','application/pdf');
        $response->headers->set('Content-Disposition','attachment; filename="'.$downloadName.'"');
        $response->headers->set('Content-Length', (string)strlen($binary));
        $response->content = $binary;
        $response->send();
    }

    /* ==========================
       Segédfüggvények
       ========================== */

    /** Oldal import + helyes lapméret és orientáció beállítás. */
    private static function importPage(FPDI $pdf, int $pageNo): void
    {
        $tpl = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tpl);
    }

    /** Általános szöveg nyomtatása (megadható szélességgel). */
    private static function printCell(FPDI $pdf, array $xy, float $width, string $text): void
    {
        $text = trim($text);
        if ($text === '') return;
        [$x,$y] = $xy;
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY($x,$y);
        $pdf->Cell($width, 5, $text, 0, 0, 'L');
    }

    /** Név nyomtatása (wrapper). */
    private static function printName(FPDI $pdf, array $xy, string $name): void
    {
        self::printCell($pdf, $xy, 102, $name);
    }

    /** Tetszőleges szöveg nyomtatása (DOB/cím) megadott szélességgel. */
    private static function printText(FPDI $pdf, array $xy, string $text, float $width = 140.0): void
    {
        self::printCell($pdf, $xy, $width, $text);
    }

    /** ÚJ: műszakok rajzolása a 23. oldal táblázatába (ASCII/Arial) – oszlopokkal */
    private static function drawShiftsPage23(FPDI $pdf, array $shifts): void
    {
        if (empty($shifts)) return;

        // === COLUMN GEOMETRY (aligned to your form) ===
        $X_DATE_L   = 21.0;  $W_DATE   = 17.0;  // Dátum
        $X_FROM_L   = 42.0;  $W_FROM   = 12.0;  // Od
        $X_TO_L     = 54.0;  $W_TO     = 12.0;  // Do
        $X_HOURS_L  = 66.0;  $W_HOURS  = 12.0;  // Spolu hodín

        $Y_START = 124.0;
        $Y_STEP  = 7.0;
        $MAX     = 16;

        $pdf->SetFont('Arial','',9);

        $n = min(count($shifts), $MAX);
        for ($i = 0; $i < $n; $i++) {
            $y = $Y_START + $i * $Y_STEP;

            $d = isset($shifts[$i]['date']) ? (string)$shifts[$i]['date'] : '';
            $d = $d && ($ts = strtotime($d)) ? date('d.m.Y', $ts) : $d;

            $f = self::fmtHm((string)($shifts[$i]['from'] ?? ''));
            $t = self::fmtHm((string)($shifts[$i]['to']   ?? ''));
            $h = (string)($shifts[$i]['hrs'] ?? '');

            // DÁTUM (left)
            $pdf->SetXY($X_DATE_L, $y);
            $pdf->Cell($W_DATE, 5, $d, 0, 0, 'L');

            // OD / DO / SPOLU (center)
            $pdf->SetXY($X_FROM_L, $y);
            $pdf->Cell($W_FROM, 5, $f, 0, 0, 'C');

            $pdf->SetXY($X_TO_L, $y);
            $pdf->Cell($W_TO, 5, $t, 0, 0, 'C');

            $pdf->SetXY($X_HOURS_L, $y);
            $pdf->Cell($W_HOURS, 5, $h, 0, 0, 'C');
        }

        $pdf->SetFont('Arial','',10);
    }

    /** ÚJ: feladatok nyomtatása a 23. oldal "Vykonávaná pracovná činnosť" oszlopába. */
    private static function drawTasksPage23(FPDI $pdf, array $tasks): void
    {
        if (empty($tasks)) return;

        // Geometry aligned to the existing table on page 23
        // (starts right after "Spolu hodín" column)
        $X_TASKS_L = 80.0;   // left edge of the tasks column
        $W_TASKS   = 115.0;  // width of the tasks column
        $Y_START   = 124.0;  // first row baseline (same as shifts)
        $Y_STEP    = 7.0;    // row spacing
        $MAX       = 16;     // visible rows on the sheet

        $pdf->SetFont('Arial','',9);

        $n = min(count($tasks), $MAX);
        for ($i = 0; $i < $n; $i++) {
            $line = trim((string)$tasks[$i]);
            if ($line === '') continue;
            $y = $Y_START + $i * $Y_STEP;
            $pdf->SetXY($X_TASKS_L, $y);
            $pdf->Cell($W_TASKS, 5, '• '.$line, 0, 0, 'L');
        }

        $pdf->SetFont('Arial','',10);
    }

    /** Névlista tisztítása. */
    private static function sanitizeNames(array $names): array
    {
        $names = array_map(static function($n){
            $n = is_string($n) ? trim($n) : '';
            return $n;
        }, $names);
        return array_values(array_filter($names, static fn($n) => $n !== ''));
    }

    /**
     * XML koordináták beolvasása a <repeat id="per_person_pages"> blokk alapján
     * + ugyanazok top-level <field page="23|24"> nevekkel is (ha ott vannak).
     */
    private static function readCoordsFromXml(?string $xmlPath): array
    {
        $out = ['23'=>[], '24'=>[]];
        if (!$xmlPath || !is_file($xmlPath)) return $out;

        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file($xmlPath);
        if ($xml === false) return $out;

        $grabText = static function($page, string $name) {
            $nodes = $page->xpath("text[@name='{$name}']") ?: [];
            if (!$nodes) return null;
            $n = $nodes[0];
            $x = isset($n['x']) ? (float)$n['x'] : null;
            $y = isset($n['y']) ? (float)$n['y'] : null;
            $w = isset($n['width']) ? (float)$n['width'] : null;
            return ($x !== null && $y !== null) ? [$x,$y,$w] : null;
        };

        // 1) From repeat block (preferált)
        $pages = $xml->xpath("//repeat[@id='per_person_pages']/page") ?: [];
        foreach ($pages as $page) {
            $ref = (string)($page['ref'] ?? '');
            if ($ref !== '23' && $ref !== '24') continue;

            if ($ref === '23') {
                if ($c = $grabText($page, 'person_name'))       $out['23']['name']  = $c;
                if ($c = $grabText($page, 'person_dob'))        $out['23']['dob']   = $c;
                if ($c = $grabText($page, 'person_address'))    $out['23']['addr']  = $c;
                if ($c = $grabText($page, 'person_first_name')) $out['23']['first'] = $c;
                if ($c = $grabText($page, 'person_last_name'))  $out['23']['last']  = $c;

                // ---- Attendance 23-on (XML) ----
                if ($c = $grabText($page, 'shifts_month'))           $out['23']['shifts_month']         = $c;
                if ($c = $grabText($page, 'shifts_total_hours'))     $out['23']['shifts_total_hours']   = $c;
                if ($c = $grabText($page, 'total_hours_in_month'))   $out['23']['total_hours_in_month'] = $c;
                if ($c = $grabText($page, 'shifts_summary'))         $out['23']['shifts_summary']       = $c;

                // ---- ÚJ: oszlop X-ek + Y start/step/max (page 23) ----
                $cols = [];
                if ($c = $grabText($page, 'shifts_col_date'))  $cols['date_x']  = (float)$c[0];
                if ($c = $grabText($page, 'shifts_col_from'))  $cols['from_x']  = (float)$c[0];
                if ($c = $grabText($page, 'shifts_col_to'))    $cols['to_x']    = (float)$c[0];
                if ($c = $grabText($page, 'shifts_col_hours')) $cols['hours_x'] = (float)$c[0];
                if ($cols) $out['23']['_shifts_cols'] = $cols;

                if ($c = $grabText($page, 'shifts_y_start'))   $out['23']['_shifts_y_start'] = (float)$c[1];
                if ($c = $grabText($page, 'shifts_y_step'))    $out['23']['_shifts_y_step']  = (float)$c[1];
                if ($c = $grabText($page, 'shifts_max_rows'))  $out['23']['_shifts_max']     = (int)round((float)$c[1]);

                // ---- ÚJ: szerződés dátuma + hónap (page 23) ----
                if ($c = $grabText($page, 'today'))          $out['23']['today']          = $c;
                if ($c = $grabText($page, 'today_sk'))       $out['23']['today_sk']       = $c;
                if ($c = $grabText($page, 'current_month'))  $out['23']['current_month']  = $c;

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
                if ($c = $grabText($page, 'person_dob'))        $out['24']['dob']   = $c;
                if ($c = $grabText($page, 'person_address'))    $out['24']['addr']  = $c;
                if ($c = $grabText($page, 'person_first_name')) $out['24']['first'] = $c;
                if ($c = $grabText($page, 'person_last_name'))  $out['24']['last']  = $c;

                // ÚJ: dátum mezők a 24. oldalon (repeat-ben)
                if ($c = $grabText($page, 'today'))    $out['24']['today']    = $c;
                if ($c = $grabText($page, 'today_sk')) $out['24']['today_sk'] = $c;

                // MEGJ: attendance NINCS 24-en (X jelölést mi tesszük rá)
            }
        }

        // 2) Top-level <field> fallbackok (23 és 24 oldalakhoz)
        $fieldAt = static function(\SimpleXMLElement $xmlDoc, string $name, string $page) {
            $nodes = $xmlDoc->xpath("//field[@name='{$name}' and @page='{$page}']") ?: [];
            if (!$nodes) return null;
            $n = $nodes[0];
            $x = isset($n['x']) ? (float)$n['x'] : null;
            $y = isset($n['y']) ? (float)$n['y'] : null;
            $w = isset($n['width']) ? (float)$n['width'] : null;
            return ($x !== null && $y !== null) ? [$x,$y,$w] : null;
        };

        // Attendance top-level 23-as oldalon is támogatott
        foreach (['shifts_month','shifts_total_hours','total_hours_in_month','shifts_summary'] as $nm) {
            if (!isset($out['23'][$nm])) {
                if ($c = $fieldAt($xml, $nm, '23')) { $out['23'][$nm] = $c; }
            }
        }
        $cols = $out['23']['_shifts_cols'] ?? [];
        if (!isset($cols['date_x'])  && ($c = $fieldAt($xml, 'shifts_col_date','23')))  $cols['date_x']  = (float)$c[0];
        if (!isset($cols['from_x'])  && ($c = $fieldAt($xml, 'shifts_col_from','23')))  $cols['from_x']  = (float)$c[0];
        if (!isset($cols['to_x'])    && ($c = $fieldAt($xml, 'shifts_col_to','23')))    $cols['to_x']    = (float)$c[0];
        if (!isset($cols['hours_x']) && ($c = $fieldAt($xml, 'shifts_col_hours','23'))) $cols['hours_x'] = (float)$c[0];
        if ($cols) $out['23']['_shifts_cols'] = $cols;

        if (!isset($out['23']['_shifts_y_start']) && ($c = $fieldAt($xml, 'shifts_y_start','23'))) $out['23']['_shifts_y_start'] = (float)$c[1];
        if (!isset($out['23']['_shifts_y_step'])  && ($c = $fieldAt($xml, 'shifts_y_step','23')))  $out['23']['_shifts_y_step']  = (float)$c[1];
        if (!isset($out['23']['_shifts_max'])     && ($c = $fieldAt($xml, 'shifts_max_rows','23'))) $out['23']['_shifts_max']    = (int)round((float)$c[1]);

        // 23-as top-level fallbackok: today / today_sk / current_month
        if (!isset($out['23']['today']) && ($c = $fieldAt($xml, 'today', '23'))) {
            $out['23']['today'] = $c;
        }
        if (!isset($out['23']['today_sk']) && ($c = $fieldAt($xml, 'today_sk', '23'))) {
            $out['23']['today_sk'] = $c;
        }
        if (!isset($out['23']['current_month']) && ($c = $fieldAt($xml, 'current_month', '23'))) {
            $out['23']['current_month'] = $c;
        }

        // 24-es top-level fallbackok: név/DOB/cím/first/last
        foreach (['dob','addr','first','last'] as $nm) {
            if (!isset($out['24'][$nm])) {
                if ($c = $fieldAt($xml, 'person_'.$nm, '24')) { $out['24'][$nm] = $c; }
            }
        }
        // 24-es top-level fallbackok: today / today_sk
        if (!isset($out['24']['today']) && ($c = $fieldAt($xml, 'today', '24'))) {
            $out['24']['today'] = $c;
        }
        if (!isset($out['24']['today_sk']) && ($c = $fieldAt($xml, 'today_sk', '24'))) {
            $out['24']['today_sk'] = $c;
        }

        return $out;
    }

    /** Normalize a time string to HH:MM (no seconds). Accepts 08:07:18, 8:7, 08.07, 0830, etc. */
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

    /**
     * Koordináta feloldása (XML-ből vagy fallbackből) + szélesség.
     */
    private static function coord(array $cx, string $page, string $key, ?array $fallbackXY, float $fallbackW): array
    {
        if (isset($cx[$page][$key]) && is_array($cx[$page][$key])) {
            $x = (float)$cx[$page][$key][0];
            $y = (float)$cx[$page][$key][1];
            $w = isset($cx[$page][$key][2]) && $cx[$page][$key][2] !== null ? (float)$cx[$page][$key][2] : $fallbackW;
            return [$x,$y,$w];
        }
        if ($fallbackXY === null) {
            return [0.0, 0.0, $fallbackW];
        }
        $x = $fallbackXY[0] ?? 0.0;
        $y = $fallbackXY[1] ?? 0.0;
        return [$x,$y,$fallbackW];
    }

    /** Slovak month name from ISO date. */
    private static function monthSkFromIso(string $iso): string
    {
        $ts = strtotime($iso ?: 'today');
        $m  = (int)date('n', $ts);
        $sk = [1=>'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'];
        return $sk[$m] ?? '';
    }

    /** Draw X marks into the 24th-page month/day grid from $shifts[] (date, from, to, hrs). */
    private static function drawAttendanceMarksPage24(FPDI $pdf, array $shifts): void
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
        $pdf->SetFont('Arial','',8.5);

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

        $pdf->SetFont('Arial','',10);
    }

}
