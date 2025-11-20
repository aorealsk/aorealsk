<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\User;
use backend\models\ContractTemplate;

class ContractBuilderController extends Controller
{
    public $enableCsrfValidation = false;

    /* ============================== GENERATE ================================= */

    public function actionGenerate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $selectedUsers = Yii::$app->request->post('users', []);
            $positionsRaw  = Yii::$app->request->post('positions', '{}');
            $positionsAll  = json_decode($positionsRaw, true) ?: [];
            $pagesInput    = Yii::$app->request->post('pages', '');
            $formDate      = Yii::$app->request->post('date');
            $partnerId     = Yii::$app->request->post('partner_id');

            // Per-user edits from preview (keeps \n and spaces)
            $editsRaw     = Yii::$app->request->post('edits', '{}');
            $userEditsAll = json_decode($editsRaw, true) ?: [];

            // Could be IDs or file paths
            $selectedTpls = Yii::$app->request->post('contractSelect', []);
            if (!is_array($selectedTpls)) {
                $selectedTpls = $selectedTpls ? [$selectedTpls] : [];
            }

            $uploadDir = Yii::getAlias('@backend/web/uploads/');
            $outputDir = Yii::getAlias('@backend/web/contracts/');
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
            if (!is_dir($outputDir)) mkdir($outputDir, 0775, true);

            // Build template sources (dedup)
            $sources = [];
            $seen = [];

            foreach ($selectedTpls as $val) {
                if ($val === '' || $val === null) continue;

                if (ctype_digit((string)$val)) {
                    $tpl = ContractTemplate::findOne((int)$val);
                    if (!$tpl) throw new \Exception("Selected template not found: {$val}");
                    $rel = '/' . ltrim($tpl->file_path, '/');
                    $abs = rtrim(Yii::getAlias('@backend/web'), '/') . $rel;
                    if (!is_file($abs)) throw new \Exception("Template file missing on server: {$tpl->file_path}");
                    $key = 'ID:' . $tpl->id;
                    if (isset($seen[$key])) continue;
                    $seen[$key] = true;
                    $sources[] = [
                        'tkey' => 'tpl_' . (int)$tpl->id,
                        'abs'  => $abs,
                        'name' => $tpl->name ?: basename($tpl->file_path, '.pdf'),
                        'id'   => (int)$tpl->id,
                        'rel'  => (string)$tpl->file_path,
                    ];
                } else {
                    $rel = '/' . ltrim($val, '/');
                    $abs = rtrim(Yii::getAlias('@backend/web'), '/') . $rel;
                    if (!is_file($abs)) throw new \Exception("Selected template not found: {$val}");
                    $key = 'REL:' . $rel;
                    if (isset($seen[$key])) continue;
                    $seen[$key] = true;
                    $rec = ContractTemplate::find()->where(['file_path' => $val])->one();
                    $sources[] = [
                        'tkey' => (string)$val,
                        'abs'  => $abs,
                        'name' => $rec ? $rec->name : basename($val, '.pdf'),
                        'id'   => $rec ? (int)$rec->id : null,
                        'rel'  => (string)$val,
                    ];
                }
            }

            // Optional ad-hoc upload (previewed "uploaded" source)
            $uploadedFile = $_FILES['pdfFile'] ?? null;
            if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
                $safeName = preg_replace('~[^a-zA-Z0-9._-]+~', '_', basename($uploadedFile['name']));
                $tmpPath  = $uploadDir . 'adhoc_' . time() . '_' . $safeName;
                if (!move_uploaded_file($uploadedFile['tmp_name'], $tmpPath)) {
                    throw new \Exception('Could not store uploaded PDF.');
                }
                $key = 'UP:' . md5($safeName . '|' . filesize($tmpPath));
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $sources[] = [
                        'tkey' => 'uploaded',
                        'abs'  => $tmpPath,
                        'name' => pathinfo($safeName, PATHINFO_FILENAME),
                        'id'   => null,
                        'rel'  => null,
                    ];
                }
            }

            if (!$sources) throw new \Exception('No template provided (select a saved template or upload a PDF).');

            // All-users list text (for the all_users block)
            $allUsersText = $this->buildAllUsersText($selectedUsers);

            $generatedFiles = [];
            $usedNames = [];

            foreach ($selectedUsers as $userId) {
                $user = User::findOne($userId);
                if (!$user) continue;

                // user key may be "123" or 123 depending on JS; support both
                $userKeyStr = (string)$user->id;
                $userKeyInt = (int)$user->id;
                $userEdits  = $userEditsAll[$userKeyStr] ?? ($userEditsAll[$userKeyInt] ?? []);

                foreach ($sources as $src) {
                    $posForThis    = $this->resolvePositionsForSource($positionsAll, $src);
                    $selectedPages = $this->parsePageRange($pagesInput, $src['abs']);

                    // determine tkey used on the client for this source
                    $tkey = isset($src['tkey']) ? (string)$src['tkey']
                         : (!empty($src['id']) ? 'tpl_' . (string)$src['id'] : (string)($src['rel'] ?? ''));

                    // merge this user's edits for this template key into positions
                    $perUserForTkey = $userEdits[$tkey] ?? [];
                    $posForThis     = $this->mergeUserEditsIntoPositions($posForThis, $perUserForTkey);

                    // unique filename
                    $slug = $this->slug($src['name'] ?: 'template');
                    $name = 'contract_' . $user->id . '_' . $slug . '.pdf';
                    $i = 2;
                    while (isset($usedNames[$name])) {
                        $name = 'contract_' . $user->id . '_' . $slug . '-' . $i . '.pdf';
                        $i++;
                    }
                    $usedNames[$name] = true;

                    $this->fillPdfUtfSafe(
                        $src['abs'],
                        $outputDir . $name,
                        $user,
                        $posForThis,
                        $selectedPages,
                        $formDate,
                        [
                            'all_users'  => $allUsersText,
                            'partner_id' => $partnerId,
                        ]
                    );

                    $generatedFiles[] = $name;
                }
            }

            $generatedFiles = array_values(array_unique($generatedFiles));
            if (!$generatedFiles) throw new \Exception('No contracts were generated (no users selected?).');

            return ['status' => 'ok', 'files' => $generatedFiles];

        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /* ========================== PARTNERS (JSON) =============================== */

    public function actionListPartners()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (class_exists('\common\models\Partner')) {
            $q = \common\models\Partner::find();
        } elseif (class_exists('\backend\models\Partner')) {
            $q = \backend\models\Partner::find();
        } else {
            return ['status' => 'ok', 'partners' => []];
        }

        $rows = $q->orderBy(['partner_name' => SORT_ASC])->all();
        $out  = [];
        foreach ($rows as $p) {
            $out[] = [
                'id'                      => (int)$p->id,
                'partner_name'            => (string)$p->partner_name,
                'address'                 => (string)($p->address ?? ''),
                'town'                    => (string)($p->town ?? ''),
                'zip'                     => (string)($p->zip ?? ''),
                'registration_number'     => (string)($p->registration_number ?? ''),
                'ICO'                     => (string)($p->ICO ?? ''),
                'DIC'                     => (string)($p->DIC ?? ''),
                'DICDPH'                  => (string)($p->DICDPH ?? ''),
                'CEO'                     => (string)($p->CEO ?? ''),
                'DELEGATE'                => (string)($p->DELEGATE ?? ''),
                'tax_number'              => (string)($p->tax_number ?? ''),
            ];
        }

        return ['status' => 'ok', 'partners' => $out];
    }

    private function fetchAllPartners(): array
    {
        if (class_exists('\common\models\Partner')) {
            return \common\models\Partner::find()->orderBy(['partner_name' => SORT_ASC])->all();
        }
        if (class_exists('\backend\models\Partner')) {
            return \backend\models\Partner::find()->orderBy(['partner_name' => SORT_ASC])->all();
        }
        return [];
    }

    /* ========================== TEMPLATES (CRUD lite) ========================= */

    public function actionSaveTemplate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uploadedFile = $_FILES['pdfFile'] ?? null;
            if (!$uploadedFile || $uploadedFile['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No PDF uploaded.');
            }

            $name     = Yii::$app->request->post('name', '');
            $safeBase = preg_replace('~[^a-zA-Z0-9._-]+~', '_', pathinfo($uploadedFile['name'], PATHINFO_FILENAME));
            $safeFile = $safeBase . '_' . time() . '.pdf';

            $baseDir = Yii::getAlias('@backend/web/uploads/templates/');
            if (!is_dir($baseDir)) { mkdir($baseDir, 0775, true); }

            $absPath = $baseDir . $safeFile;
            if (!move_uploaded_file($uploadedFile['tmp_name'], $absPath)) {
                throw new \Exception('Could not save template.');
            }

            $tpl = new ContractTemplate();
            $tpl->name      = $name ?: $safeBase;
            $tpl->file_path = '/uploads/templates/' . $safeFile;
            if (!$tpl->save(false)) {
                @unlink($absPath);
                throw new \Exception('Database error saving template.');
            }

            return ['status' => 'ok', 'templates' => $this->listTemplatesData()];

        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function actionListTemplates()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'ok', 'templates' => $this->listTemplatesData()];
    }

    public function actionStreamTemplate($id)
    {
        $tpl = ContractTemplate::findOne((int)$id);
        if (!$tpl) throw new NotFoundHttpException('Template not found.');
        $abs = rtrim(Yii::getAlias('@backend/web'), '/') . '/' . ltrim($tpl->file_path, '/');
        if (!is_file($abs)) throw new NotFoundHttpException('Template file missing.');
        return Yii::$app->response->sendFile($abs, null, ['inline' => true]);
    }

    private function listTemplatesData(): array
    {
        $rows = ContractTemplate::find()->orderBy(['id' => SORT_DESC])->all();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'        => (int)$r->id,
                'name'      => (string)$r->name,
                'file_path' => (string)$r->file_path,
            ];
        }
        return $out;
    }

    /* ========================== PDF ENGINE & HELPERS ========================== */

    private function resolvePositionsForSource(array $positionsAll, array $src): array
    {
        $candidateKeys = [];
        if (!empty($src['tkey'])) $candidateKeys[] = (string)$src['tkey'];
        if (!empty($src['id'])) {
            $candidateKeys[] = (string)$src['id'];
            $candidateKeys[] = 'tpl_' . (string)$src['id'];
        }
        if (!empty($src['rel'])) $candidateKeys[] = (string)$src['rel'];

        foreach ($candidateKeys as $k) {
            if (isset($positionsAll[$k]) && is_array($positionsAll[$k])) {
                return $positionsAll[$k];
            }
        }

        if ($positionsAll && count(array_filter(array_keys($positionsAll), 'is_numeric')) === count($positionsAll)) {
            return $positionsAll;
        }
        return [];
    }

    private function slug(string $s): string
    {
        $s = preg_replace('~[^\pL\d]+~u', '-', $s);
        $s = trim($s, '-');
        $s = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        $s = preg_replace('~[^-\w]+~', '', $s);
        $s = preg_replace('~-+~', '-', $s);
        return strtolower($s ?: 'file');
    }

    private function buildAllUsersText(array $userIds): string
    {
        $names = [];
        foreach ($userIds as $uid) {
            $u = User::findOne($uid);
            if ($u) {
                $full = trim(($u->name_first ?? '') . ' ' . ($u->name_last ?? ''));
                if ($full !== '') $names[] = $full;
            }
        }
        return implode("\n", $names);
    }

    private function parsePageRange(string $input, string $pdfPath): array
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);

        if (trim($input) === '') return range(1, $pageCount);

        $result = [];
        foreach (explode(',', $input) as $part) {
            $part = trim($part);
            if ($part === '') continue;

            if (strpos($part, '-') !== false) {
                [$start, $end] = array_map('intval', explode('-', $part));
                if ($start > $end) { [$start, $end] = [$end, $start]; }
                for ($i = $start; $i <= $end; $i++) {
                    if ($i >= 1 && $i <= $pageCount) $result[] = $i;
                }
            } else {
                $n = (int)$part;
                if ($n >= 1 && $n <= $pageCount) $result[] = $n;
            }
        }

        $result = array_values(array_unique($result));
        sort($result);
        return $result;
    }

    private function createPdfEngine(): array
    {
        $isTcpdf = false;
        if (class_exists('\setasign\Fpdi\Tcpdf\Fpdi')) {
            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $isTcpdf = true;
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setFontSubsetting(true);
            $pdf->SetFont('dejavusans', '', 10, '', true);
        } else {
            $pdf = new \setasign\Fpdi\Fpdi();
            $isTcpdf = false;

            $fontDirA = rtrim(Yii::getAlias('@backend/web/fonts'), '/') . '/DejaVuSans.ttf';
            $fontDirB = rtrim(Yii::getAlias('@backend/fonts'), '/') . '/DejaVuSans.ttf';

            try {
                if (is_file($fontDirA) || is_file($fontDirB)) {
                    if (!defined('FPDF_FONTPATH')) {
                        define('FPDF_FONTPATH', rtrim(Yii::getAlias('@backend/web/fonts'), '/') . '/');
                    }
                    $path = is_file($fontDirA) ? $fontDirA : $fontDirB;
                    @$pdf->AddFont('DejaVu', '', $path, true);
                    $pdf->SetFont('DejaVu', '', 10);
                } else {
                    $pdf->SetFont('Helvetica', '', 10);
                }
            } catch (\Throwable $e) {
                $pdf->SetFont('Helvetica', '', 10);
            }
        }

        $pdf->SetTextColor(0, 0, 0);
        return [$pdf, $isTcpdf];
    }

    private function utfToCp1250(string $s): string
    {
        $converted = @iconv('UTF-8', 'Windows-1250//TRANSLIT//IGNORE', $s);
        if ($converted === false) {
            $converted = @mb_convert_encoding($s, 'Windows-1250', 'UTF-8');
        }
        return $converted !== false ? $converted : $s;
    }

    /**
 * Resolve user's profession (Odbor) from study_plan_type_id → study_plan_types.name
 */
private function resolveProfession(?\common\models\User $user): string
    {
    if (!$user || empty($user->study_plan_type_id)) {
        return '';
    }

    // If you have an AR relation, you can do:
    // return $user->studyPlanType->name ?? '';

    // Generic, relation-free lookup (safe on any project):
    $name = \common\models\StudyPlanTypes::find()
        ->select('name')
        ->where(['id' => (int)$user->study_plan_type_id])
        ->scalar();

    return $name ? (string)$name : '';
    }

private function fillPdfUtfSafe(
    string $templatePath,
    string $outputPath,
    User $user,
    array $positions,
    array $selectedPages,
    ?string $formDate,
    array $extra = []
    ) {
    [$pdf, $isTcpdf] = $this->createPdfEngine();

    $pageCount = $pdf->setSourceFile($templatePath);
    if (empty($selectedPages)) {
        $selectedPages = range(1, $pageCount);
    }

    // ------------ FONT & LAYOUT CALIBRATION ------------
    $SMALL_FONT_SIZE    = 9;    // small lists/monospace blocks
    $GENERIC_FONT_SZ    = 8;    // default elsewhere

    // Mesacny cinnost tuning
    $MESACNY_FONT_SIZE  = 8;    // text size inside the 1-column table
    $MESACNY_WRAP_WIDTH = 28;   // chars per line inside one cell

    $CAL = [
        'attendance_monthly' => [
            'rowH'    => 6.60,
            'topPad'  => 1.10,
            'leftPad' => -1.8,
            'colW'    => [10, 5, 5, 5], // date, in, out, total
            'gap'     => 2,
        ],
        'all_users' => [
            'rowH'    => 9.40,
            'topPad'  => 1.10,
            'leftPad' => 0.40,
        ],
        // gaps for "mesačná činnosť"
        'mesacny_cinnost' => [
            'rowH'      => 5.80,  // base line height
            'topPad'    => 0.80,
            'leftPad'   => 0.00,
            'lineGap'   => -2.50, // gap BETWEEN LINES within a cell
            'cellGapPx' => 4.00,  // visual gap BETWEEN CELLS in preview px (scaled to PDF)
        ],
    ];
    // ---------------------------------------------------

    // Build base data once
    $data = $this->buildUserData($user, $formDate, $extra ?: []);
    if ($extra) {
        $data = array_merge($data, $extra);
    }

    /**
     * Format monthly attendance as fixed columns
     * AND respect manual blank lines:
     * - Each input line (separated by "\n") -> one table row.
     * - Empty line -> blank row (jump one cell).
     */
    $formatMonthly = function (string $txt) use ($CAL): array {
        $txt = str_replace(["\r\n", "\r"], "\n", (string)$txt);
        $rawLines = explode("\n", $txt);

        $out = [];
        $w   = $CAL['attendance_monthly']['colW'];
        $gap = str_repeat(' ', max(0, (int)$CAL['attendance_monthly']['gap']));

        foreach ($rawLines as $raw) {
            // Blank line => blank row (jump)
            if (trim($raw) === '') {
                $out[] = '';
                continue;
            }

            $line  = trim($raw);
            // Try "date; in; out; total" first
            $parts = preg_split('/\s*;\s*|\s{2,}|\t/u', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) < 4) {
                // Fallback: split by single spaces
                $parts = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            }

            if (count($parts) >= 4) {
                $date  = $parts[0];
                $in    = $parts[1];
                $outT  = $parts[2];
                $total = $parts[3];

                $out[] =
                    str_pad($date,  $w[0], ' ', STR_PAD_RIGHT) . $gap .
                    str_pad($in,    $w[1], ' ', STR_PAD_RIGHT) . $gap .
                    str_pad($outT,  $w[2], ' ', STR_PAD_RIGHT) . $gap .
                    str_pad($total, $w[3], ' ', STR_PAD_RIGHT);
            } else {
                // If it doesn't match 4 parts, just print as-is
                $out[] = $line;
            }
        }

        return $out;
    };

    $baseLineH = 5.0;

    foreach ($selectedPages as $pageNo) {
        if ($pageNo < 1 || $pageNo > $pageCount) continue;

        $tplIdx = $pdf->importPage($pageNo);
        $size   = $pdf->getTemplateSize($tplIdx);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tplIdx);

        if (empty($positions[$pageNo]) || !is_array($positions[$pageNo])) continue;

        foreach ($positions[$pageNo] as $uniqueKey => $coords) {
            if (!is_array($coords)) continue;

            $field = $coords['source'] ?? $uniqueKey;

            // Edited value (from preview) wins over base data
            $hasEdited = array_key_exists('value', $coords);
            $editedVal = $hasEdited ? (string)$coords['value'] : null;
            $resolved  = ($editedVal !== null && $editedVal !== '')
                ? $editedVal
                : (string)($data[$field] ?? '');
            if ($resolved === '') continue;

            // Coords (preview px -> PDF units)
            $pxX = isset($coords['x']) ? (float)$coords['x'] : 0.0;
            $pxY = isset($coords['y']) ? (float)$coords['y'] : 0.0;
            $ow  = isset($coords['ow']) ? (float)$coords['ow'] : null;
            $oh  = isset($coords['oh']) ? (float)$coords['oh'] : null;

            if ($ow && $oh && $ow > 0 && $oh > 0) {
                $x = $pxX * ($size['width']  / $ow);
                $y = $pxY * ($size['height'] / $oh);
            } else {
                $x = $pxX / 3.75;
                $y = ($pxY / 3.50) - 2.2;
            }
            $usableW = max(10, $size['width'] - $x - 10);

            /* -------- SPECIAL BLOCKS -------- */

            // 1) Attendance table – now respects blank lines as empty rows
            if ($field === 'attendance_monthly') {
                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusansmono', '', $SMALL_FONT_SIZE, '', true);
                } else {
                    $pdf->SetFont('Courier', '', $SMALL_FONT_SIZE);
                }

                $lines = $formatMonthly($resolved);
                if (!$isTcpdf) {
                    $lines = array_map(fn($s) => $this->utfToCp1250($s), $lines);
                }

                $rowH   = (float)$CAL['attendance_monthly']['rowH'];
                $topPad = (float)$CAL['attendance_monthly']['topPad'];
                $left   = (float)$CAL['attendance_monthly']['leftPad'];

                $curY = $y + $topPad;
                foreach ($lines as $ln) {
                    $ln = rtrim($ln, "\t ");
                    $pdf->SetXY($x + $left, $curY);
                    if ($isTcpdf) {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L', false, '', 0);
                    } else {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L');
                    }
                    // even if $ln === '' we still move down one row: "jump one place"
                    $curY += $rowH;
                    if ($curY > ($size['height'] - 5)) break;
                }

                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusans', '', $GENERIC_FONT_SZ, '', true);
                } else {
                    $pdf->SetFont('Helvetica', '', $GENERIC_FONT_SZ);
                }
                continue;
            }

            // 2) All users list
            if ($field === 'all_users') {
                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusans', '', $SMALL_FONT_SIZE, '', true);
                } else {
                    $pdf->SetFont('Helvetica', '', $SMALL_FONT_SIZE);
                }

                $txt = str_replace(["\r\n", "\r"], "\n", $resolved);
                if (strpos($txt, "\n") === false) {
                    $parts = preg_split('/\s*[,;]\s*/u', trim($txt), -1, PREG_SPLIT_NO_EMPTY);
                    if ($parts && count($parts) > 1) {
                        $txt = implode("\n", $parts);
                    }
                }
                $lines = explode("\n", $txt);
                if (!$isTcpdf) {
                    $lines = array_map(fn($s) => $this->utfToCp1250($s), $lines);
                }

                $rowH   = (float)$CAL['all_users']['rowH'];
                $topPad = (float)$CAL['all_users']['topPad'];
                $left   = (float)$CAL['all_users']['leftPad'];

                $curY = $y + $topPad;
                foreach ($lines as $ln) {
                    $ln = rtrim($ln, "\t ");
                    $pdf->SetXY($x + $left, $curY);
                    if ($isTcpdf) {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L', false, '', 0);
                    } else {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L');
                    }
                    $curY += $rowH;
                    if ($curY > ($size['height'] - 5)) break;
                }

                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusans', '', $GENERIC_FONT_SZ, '', true);
                } else {
                    $pdf->SetFont('Helvetica', '', $GENERIC_FONT_SZ);
                }
                continue;
            }

            // 3) Mesačná činnosť – 1-column virtual table
            if ($field === 'mesacny_cinnost') {
                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusansmono', '', $MESACNY_FONT_SIZE, '', true);
                } else {
                    $pdf->SetFont('Courier', '', $MESACNY_FONT_SIZE);
                }

                // Use your helper to split into “cells”
                $cells = $this->splitActivitiesToCells((string)$resolved);

                // Convert the requested cell gap (in px) into PDF units
                $cellGapPx = (float)($CAL['mesacny_cinnost']['cellGapPx'] ?? 4.0);
                $cellGap   = ($ow && $oh)
                    ? ($cellGapPx * ($size['height'] / $oh))
                    : 1.2;

                $rowH    = (float)$CAL['mesacny_cinnost']['rowH'];
                $lineGap = (float)($CAL['mesacny_cinnost']['lineGap'] ?? 0.0);
                $topPad  = (float)$CAL['mesacny_cinnost']['topPad'];
                $left    = (float)$CAL['mesacny_cinnost']['leftPad'];

                $curY = $y + $topPad;

                foreach ($cells as $cellText) {
                    $wrapped = $this->hardWrapUtf($cellText, $MESACNY_WRAP_WIDTH);
                    $lines   = $wrapped === '' ? [] : explode("\n", $wrapped);
                    if (!$isTcpdf) {
                        $lines = array_map(fn($s) => $this->utfToCp1250($s), $lines);
                    }

                    foreach ($lines as $ln) {
                        $ln = rtrim($ln, "\t ");
                        $pdf->SetXY($x + $left, $curY);
                        if ($isTcpdf) {
                            $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L', false, '', 0);
                        } else {
                            $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L');
                        }
                        $curY += ($rowH + $lineGap); // between lines
                        if ($curY > ($size['height'] - 5)) break 2;
                    }

                    $curY += $cellGap;              // between cells
                    if ($curY > ($size['height'] - 5)) break;
                }

                if ($isTcpdf) {
                    @ $pdf->SetFont('dejavusans', '', $GENERIC_FONT_SZ, '', true);
                } else {
                    $pdf->SetFont('Helvetica', '', $GENERIC_FONT_SZ);
                }
                continue;
            }

            /* -------- GENERIC FIELDS -------- */

            if ($isTcpdf) {
                @ $pdf->SetFont('dejavusans', '', $GENERIC_FONT_SZ, '', true);
            } else {
                $pdf->SetFont('Helvetica', '', $GENERIC_FONT_SZ);
            }

            $text = str_replace(["\r\n", "\r"], "\n", $resolved);
            $text = str_replace(';', '', $text);
            $text = preg_replace('~\s00:00\b~', '', $text);

            $renderText  = $isTcpdf ? $text : $this->utfToCp1250($text);
            $isMultiline = (strpos($renderText, "\n") !== false);

            if ($isMultiline) {
                $rowH  = $baseLineH;
                $lines = explode("\n", $renderText);
                $curY  = $y;
                foreach ($lines as $ln) {
                    $ln = rtrim($ln, "\t ");
                    $pdf->SetXY($x, $curY);
                    if ($isTcpdf) {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L', false, '', 0);
                    } else {
                        $pdf->Cell($usableW, $rowH, $ln, 0, 1, 'L');
                    }
                    $curY += $rowH;
                    if ($curY > ($size['height'] - 5)) break;
                }
            } else {
                $pdf->SetXY($x, $y);
                if ($isTcpdf) {
                    $pdf->Cell($usableW, $baseLineH, $renderText, 0, 1, 'L', false, '', 0);
                } else {
                    $pdf->Write($baseLineH, $renderText);
                }
            }
        }
    }

    $pdf->Output($outputPath, 'F');
    }   



private function addSkDateFields(array $data, ?string $formDate): array
    {
    try {
        $dateStr = $formDate ?: ($data['date'] ?? null);
        $dt = $dateStr ? new \DateTime($dateStr) : null;
    } catch (\Throwable $e) { $dt = null; }

    if ($dt) {
        $mNom = [1=>'január', 2=>'február', 3=>'marec', 4=>'apríl', 5=>'máj', 6=>'jún',
                 7=>'júl',    8=>'august',  9=>'september', 10=>'október', 11=>'november', 12=>'december'];
        $mGen = [1=>'januára', 2=>'februára', 3=>'marca', 4=>'apríla', 5=>'mája', 6=>'júna',
                 7=>'júla',    8=>'augusta',  9=>'septembra', 10=>'októbra', 11=>'novembra', 12=>'decembra'];

        $d  = (int)$dt->format('j');   // 1–31
        $m  = (int)$dt->format('n');   // 1–12
        $y  = (int)$dt->format('Y');

        $data['date_sk']       = sprintf('%d. %d. %d', $d, $m, $y);     // 15. 10. 2025
        $data['month_sk']      = $mNom[$m];                             // október
        $data['month_sk_gen']  = $mGen[$m];                             // októbra
        $data['date_words_sk'] = sprintf('%d. %s %d', $d, $mGen[$m], $y); // 15. októbra 2025
    } else {
        $data['date_sk']       = '';
        $data['month_sk']      = '';
        $data['month_sk_gen']  = '';
        $data['date_words_sk'] = '';
    }

    return $data;
    }


/**
 * Split "activities" into 1-column table *cells*.
 * Rules:
 *  - Each **Enter** starts a new cell.
 *  - A **"- "** bullet (at the start of a line or after whitespace) also starts a new cell.
 *  - Leading bullets are stripped; inner hyphens like "oceľobetónové" do NOT split.
 */
private function splitActivitiesToCells(string $text): array
    {
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));
    if ($text === '') return [];

    // First split on manual Enters
    $lines = preg_split('/\n+/', $text);

    $cells = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') { $cells[] = ''; continue; }

        // If a line has multiple bullets, split them; otherwise keep as one cell.
        // Bullets are "- " at start or after whitespace.
        $parts = preg_split('/(?:^|\s)-\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') $cells[] = $p;
        }
    }
    return $cells;
    }

/**
 * Hard-wrap UTF-8 text at $max characters per line.
 * - Keeps existing \n as item separators
 * - Breaks at last space before limit when possible
 * - If a single token exceeds the limit, hard-cuts it
 */
private function hardWrapUtf(string $text, int $max = 25): string
    {
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));
    if ($text === '' || $max <= 0) return $text;

    $outLines = [];
    foreach (explode("\n", $text) as $line) {
        $line = trim($line);
        if ($line === '') { $outLines[] = ''; continue; }

        while (mb_strlen($line, 'UTF-8') > $max) {
            $chunk = mb_substr($line, 0, $max, 'UTF-8');

            $breakPos = -1;
            $spacePos = mb_strrpos($chunk, ' ', 0, 'UTF-8');
            if ($spacePos !== false) $breakPos = (int)$spacePos;

            if ($breakPos < 0) {
                foreach ([',',';','/','-','·','•','|',':'] as $sep) {
                    $p = mb_strrpos($chunk, $sep, 0, 'UTF-8');
                    if ($p !== false) { $breakPos = (int)$p + 1; break; }
                }
            }

            if ($breakPos <= 0) {
                $outLines[] = $chunk;
                $line = mb_substr($line, $max, null, 'UTF-8');
            } else {
                $outLines[] = trim(mb_substr($line, 0, $breakPos, 'UTF-8'));
                $line = ltrim(mb_substr($line, $breakPos, null, 'UTF-8'));
            }
        }
        if ($line !== '') $outLines[] = $line;
    }
    return implode("\n", $outLines);
    }


/**
 * Wrap Mesacny Cinnost to fixed width:
 * - Breaks long lines at word boundaries up to $width chars (hard-cut if needed).
 * - Every *rendered* line starts with "- " when $dashEveryLine is true.
 * - A *blank line* is inserted between activities when the source had a manual Enter
 *   (i.e., when there is a newline between two activity lines).
 */
private function wrapActivitiesUtf(string $text, int $width = 28, bool $dashEveryLine = true, bool $blankBetweenItems = true): array
    {
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));
    if ($text === '') return [];

    // Split on single newlines but remember blanks for "Enter = gap"
    $rawLines = explode("\n", $text);

    $out = [];
    $prevWasItem = false;

    foreach ($rawLines as $raw) {
        $raw = trim($raw);

        if ($raw === '') {
            // manual Enter => add one blank row (visual gap), but only if last was an item
            if ($blankBetweenItems && $prevWasItem) {
                $out[] = ''; // blank line advances one row height
                $prevWasItem = false;
            }
            continue;
        }

        // word-wrap this activity line into segments of max $width chars
        $line = $raw;
        while (mb_strlen($line, 'UTF-8') > $width) {
            $chunk = mb_substr($line, 0, $width, 'UTF-8');

            // prefer to break at space; else at separators; else hard cut
            $breakPos = -1;
            $spacePos = mb_strrpos($chunk, ' ', 0, 'UTF-8');
            if ($spacePos !== false) $breakPos = (int)$spacePos;

            if ($breakPos < 0) {
                foreach ([',',';','/','-','·','•','|',':'] as $sep) {
                    $p = mb_strrpos($chunk, $sep, 0, 'UTF-8');
                    if ($p !== false) { $breakPos = (int)$p + 1; break; } // keep sep
                }
            }

            if ($breakPos <= 0) {
                $render = $chunk;
                $line   = mb_substr($line, $width, null, 'UTF-8');
            } else {
                $render = trim(mb_substr($line, 0, $breakPos, 'UTF-8'));
                $line   = ltrim(mb_substr($line, $breakPos, null, 'UTF-8'));
            }

            $out[] = ($dashEveryLine ? '- ' : '') . $render;
        }
        if ($line !== '') {
            $out[] = ($dashEveryLine ? '- ' : '') . $line;
        }

        $prevWasItem = true;
    }

    return $out;
    }


    /**
 * Wrap a paragraph by character limit so it fits a single PDF table cell.
 * - Treats all original newlines as spaces (Enter ≠ new row)
 * - Each wrapped visual line is prefixed with $bullet (e.g., "- ")
 * - Breaks at the last space before $limit; hard-cuts long tokens
 */
private function wrapByCharLimitForCell(string $text, int $limit = 36, string $bullet = '- '): string
    {
    // Normalize: collapse all whitespace/newlines to a single space
    $t = str_replace(["\r\n", "\r", "\n"], ' ', trim($text));
    $t = preg_replace('/\s+/u', ' ', $t);

    // If user already typed a starting bullet, remove it to avoid double bullets
    $t = preg_replace('~^\s*[-•]+\s*~u', '', $t);

    if ($t === '' || $limit <= 0) return '';

    $out = [];
    while ($t !== '') {
        if (mb_strlen($t, 'UTF-8') <= $limit) {
            $out[] = $bullet . rtrim($t);
            break;
        }

        $chunk = mb_substr($t, 0, $limit, 'UTF-8');

        // Prefer breaking at the last space within the chunk
        $breakPos = mb_strrpos($chunk, ' ', 0, 'UTF-8');
        if ($breakPos === false || $breakPos < 1) {
            // No space: hard cut
            $line = $chunk;
            $t    = mb_substr($t, $limit, null, 'UTF-8');
        } else {
            $line = mb_substr($t, 0, $breakPos, 'UTF-8');
            $t    = ltrim(mb_substr($t, $breakPos, null, 'UTF-8'));
        }

        $out[] = $bullet . rtrim($line);
    }

    return implode("\n", $out);
    }





    /* ---------- Per-user edits merge helpers ---------- */

    private function applyUserEditsToPositions(array $positionsForSrc, array $userEdits, string $tkey): array
    {
        if (empty($userEdits) || empty($tkey)) return $positionsForSrc;
        if (empty($userEdits[$tkey]) || !is_array($userEdits[$tkey])) return $positionsForSrc;

        foreach ($userEdits[$tkey] as $pageNo => $items) {
            if (!is_array($items)) continue;
            if (!isset($positionsForSrc[$pageNo]) || !is_array($positionsForSrc[$pageNo])) continue;

            foreach ($items as $uniqueKey => $payload) {
                if (!is_array($payload)) continue;
                if (!array_key_exists('value', $payload)) continue;

                if (isset($positionsForSrc[$pageNo][$uniqueKey])) {
                    $positionsForSrc[$pageNo][$uniqueKey]['value'] = (string)$payload['value'];
                }
            }
        }
        return $positionsForSrc;
    }

    private function mergeUserEditsIntoPositions(array $positions, array $userEdits): array
    {
        if (!$userEdits) return $positions;

        foreach ($userEdits as $pageNo => $byKey) {
            if (!is_array($byKey)) continue;
            if (!isset($positions[$pageNo]) || !is_array($positions[$pageNo])) continue;

            foreach ($byKey as $uniqueKey => $edit) {
                if (!is_array($edit) || !array_key_exists('value', $edit)) continue;
                if (isset($positions[$pageNo][$uniqueKey]) && is_array($positions[$pageNo][$uniqueKey])) {
                    $positions[$pageNo][$uniqueKey]['value'] = (string)$edit['value'];
                }
            }
        }
        return $positions;
    }

    private function preserveMultipleSpaces(string $s): string
    {
        return preg_replace_callback('/ {2,}/', function ($m) {
            $len = strlen($m[0]);
            return ' ' . str_repeat(chr(160), $len - 1);
        }, $s);
    }

    private function inflateNewlinesForSpacing(string $txt, float $desiredLineH, float $baseLineH = 5.0): string
    {
        $ratio = $desiredLineH / max(0.1, $baseLineH);
        if ($ratio <= 1.01) {
            return $txt;
        }
        $lines = explode("\n", $txt);
        $extra = max(0, (int)floor(($ratio - 1.2)));
        if ($extra === 0) {
            return $txt;
        }
        $gap = str_repeat("\n \n", $extra);
        return implode($gap . "\n", $lines);
    }

    /* ========================== DATA BUILDERS ================================= */

    private function buildUserData(User $user, ?string $formDate, array $extra = []): array
    {
        $data = [
            'id'            => $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
            'name_first'    => $user->name_first,
            'name_last'     => $user->name_last,
            'birthdate'     => $user->birthdate,
            'userclassroom' => $user->userclassroom,
            'street'        => $user->street,
            'street_no'     => $user->street_no,
            'zip'           => $user->zip,
            'city'          => $user->city,
            'phone'         => $user->phone,
            'iban'          => $user->iban,
        ];

        // Guardian (first if exists)
        $guardian = \common\models\UserGuardian::find()->where(['user_id' => $user->id])->one();
        if ($guardian) {
            $data += [
                'guardian_name'      => $guardian->name,
                'guardian_relation'  => $guardian->relation,
                'guardian_phone'     => $guardian->phone,
                'guardian_email'     => $guardian->email,
                'guardian_street'    => $guardian->street,
                'guardian_street_no' => $guardian->street_no,
                'guardian_zip'       => $guardian->zip,
                'guardian_city'      => $guardian->city,
            ];
        }

        // Last attendance
        $attendance = \common\models\UserAttendance::find()
            ->where(['userId' => $user->id])
            ->orderBy(['uaDate' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($attendance) {
            $data += [
                'attendance_date' => $attendance->uaDate,
                'attendance_in'   => $attendance->inTime,
                'attendance_out'  => $attendance->outTime,
            ];
        }

        // Monthly attendance
        $monthly = $this->buildMonthlyAttendance($user->id, $formDate);
        $data['attendance_total']   = $monthly['attendance_total'];
        $data['attendance_monthly'] = $monthly['attendance_monthly'];

        // NEW: Monthly activities, based on study plan + month
        $data['mesacny_cinnost'] = $this->buildMonthlyActivitiesText($user, $formDate);

        // Company
        $company = \common\models\MyCompanies::find()->limit(1)->one();
        if ($company) {
            $data += [
                'company_name'   => $company->company_name,
                'address'        => $company->address,
                'zip'            => $company->zip,
                'town'           => $company->town,
                'ICO'            => $company->ICO,
                'DIC'            => $company->DIC,
                'DICDPH'         => $company->DICDPH,
                'CEO'            => $company->CEO,
                'DELEGATE'       => $company->DELEGATE,
                'company_email'  => $company->email,
                'company_phone'  => $company->phone,
                'company_iban'   => $company->iban,
                'bank_name'      => $company->bank_name,
            ];
        }

        // Partner / School
        $partner = null;
        if (!empty($extra['partner_id'])) {
            $partner = $this->findPartnerById($extra['partner_id']);
        }
        if (!$partner) {
            if (class_exists('\common\models\Partner')) {
                $partner = \common\models\Partner::find()->orderBy(['partner_name' => SORT_ASC])->limit(1)->one();
            } elseif (class_exists('\backend\models\Partner')) {
                $partner = \backend\models\Partner::find()->orderBy(['partner_name' => SORT_ASC])->limit(1)->one();
            }
        }
        if ($partner) {
            $data += [
                'partner_name'                => (string)$partner->partner_name,
                'partner_address'             => (string)($partner->address ?? ''),
                'partner_town'                => (string)($partner->town ?? ''),
                'partner_zip'                 => (string)($partner->zip ?? ''),
                'partner_registration_number' => (string)($partner->registration_number ?? ''),
                'partner_ICO'                 => (string)($partner->ICO ?? ''),
                'partner_DIC'                 => (string)($partner->DIC ?? ''),
                'partner_DICDPH'              => (string)($partner->DICDPH ?? ''),
                'partner_CEO'                 => (string)($partner->CEO ?? ''),
                'partner_DELEGATE'            => (string)($partner->DELEGATE ?? ''),
                'partner_tax_number'          => (string)($partner->tax_number ?? ''),
            ];
        }

        if (array_key_exists('all_users', $extra)) $data['all_users'] = $extra['all_users'];

        return $data;
    }

    private function findPartnerById($id)
    {
        if (!$id) return null;
        $id = (int)$id;
        if ($id <= 0) return null;

        if (class_exists('\common\models\Partner')) {
            $m = \common\models\Partner::findOne($id);
            if ($m) return $m;
        }
        if (class_exists('\backend\models\Partner')) {
            $m = \backend\models\Partner::findOne($id);
            if ($m) return $m;
        }
        return null;
    }

    private function buildMonthlyAttendance(int $userId, ?string $dateFromForm): array
    {
        $base = $dateFromForm ? \DateTime::createFromFormat('Y-m-d', $dateFromForm) : new \DateTime('now');
        if (!$base) { $base = new \DateTime('now'); }

        $monthStart = (clone $base)->modify('first day of this month')->setTime(0, 0, 0);
        $monthEnd   = (clone $base)->modify('last day of this month')->setTime(23, 59, 59);

        $rows = \common\models\UserAttendance::find()
            ->where(['userId' => $userId])
            ->andWhere(['between', 'uaDate', $monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->orderBy(['uaDate' => SORT_ASC, 'inTime' => SORT_ASC])
            ->all();

        $totalSeconds = 0;
        $lines = [];

        foreach ($rows as $r) {
            $dStr = (new \DateTime($r->uaDate))->format('Y-m-d');

            $fromStr = $r->inTime  ? substr($r->inTime, 0, 5)  : '';
            $toStr   = $r->outTime ? substr($r->outTime, 0, 5) : '';

            $in  = $r->inTime  ? \DateTime::createFromFormat('Y-m-d H:i:s', $dStr.''.($r->inTime  ?? '00:00:00')) : null;
            $out = $r->outTime ? \DateTime::createFromFormat('Y-m-d H:i:s', $dStr.''.($r->outTime ?? '00:00:00')) : null;

            $diff = 0;
            if ($in && $out) {
                $diff = max(0, $out->getTimestamp() - $in->getTimestamp());
                $totalSeconds += $diff;
            }

            $lines[] = sprintf('%s; %s; %s; %s', $dStr, $fromStr, $toStr, gmdate('H:i', $diff));
        }

        $totalFormatted = sprintf('%02d:%02d',
            intdiv($totalSeconds, 3600),
            intdiv($totalSeconds % 3600, 60)
        );

        return [
            'attendance_total'   => $totalFormatted,
            'attendance_monthly' => implode("\n", $lines),
        ];
    }

    /**
     * Build activities list text for the user’s study plan and month.
     * Tries DB first (generic table/column names), then falls back to
     * built-in curricula keyed by the plan *name* (no 'code' column required).
     */
    private function buildMonthlyActivitiesText(User $user, ?string $dateFromForm): string
    {
        // Plan id (nullable)
        $planId = null;
        if ($user->hasAttribute('study_plan_type_id')) {
            $planId = (int)$user->getAttribute('study_plan_type_id');
        }

        // Target month 1..12
        $base = $dateFromForm ? \DateTime::createFromFormat('Y-m-d', $dateFromForm) : new \DateTime('now');
        if (!$base) { $base = new \DateTime('now'); }
        $monthNo = (int)$base->format('n');

        /* ---------- 1) DB attempt (flexible) ---------- */
        if ($planId) {
            $db = Yii::$app->db;
            $schema = $db->schema;

            $pick = function(array $haystack, array $try) {
                foreach ($try as $c) if (in_array($c, $haystack, true)) return $c;
                return null;
            };

            foreach (['study_plan_activities', 'study_plan_months'] as $table) {
                $t = $schema->getTableSchema($table, true);
                if (!$t) continue;

                $cols = array_keys($t->columns);
                $planCol  = $pick($cols, ['study_plan_type_id','plan_type_id','study_plan_id','plan_id','type_id']);
                $monthCol = $pick($cols, ['month','month_no','month_index','month_num']);
                $textCol  = $pick($cols, ['activities','activity','items','text','name','title','description','content']);
                if (!$planCol || !$monthCol || !$textCol) continue;

                $sql = "SELECT {$db->quoteColumnName($textCol)} AS txt
                        FROM {$db->quoteTableName($table)}
                        WHERE {$db->quoteColumnName($planCol)} = :pid
                          AND {$db->quoteColumnName($monthCol)} = :m
                        ORDER BY 1 ASC";
                $rows = $db->createCommand($sql, [':pid' => $planId, ':m' => $monthNo])->queryAll();

                $list = [];
                foreach ($rows as $r) {
                    $txt = trim((string)$r['txt']);
                    if ($txt === '') continue;
                    foreach (explode("\n", str_replace(["\r\n","\r"], "\n", $txt)) as $line) {
                        $line = trim($line);
                        if ($line !== '') $list[] = $line;
                    }
                }
                if ($list) {
                    $list = array_map(fn($s) => preg_match('~^[-•]~u', $s) ? $s : ('- '.$s), $list);
                    return implode("\n", $list);
                }
            }
        }

        /* ---------- 2) Fallback by plan NAME (safe even if no 'code' col) ---------- */
        $planNameNorm = '';
        if ($planId) {
            try {
                $schema = Yii::$app->db->schema->getTableSchema('study_plan_types', true);
                if ($schema && isset($schema->columns['name'])) {
                    $row = Yii::$app->db->createCommand(
                        "SELECT name FROM study_plan_types WHERE id=:id"
                    )->bindValue(':id', $planId)->queryOne();
                    if ($row) $planNameNorm = mb_strtolower((string)$row['name']);
                }
            } catch (\Throwable $e) {
                // ignore, fall back to mappings
            }
        }

        $has = function(string $needle) use ($planNameNorm): bool {
            return $planNameNorm !== '' && mb_strpos($planNameNorm, mb_strtolower($needle)) !== false;
        };

        $pickMonth = fn(array $map, int $m) => ($map[$m] ?? []);

        // 3661 H murár – 2. ročník
        $murar2 = [
            9 => [
                'BOZP pri murovaní, PO, OOPP, ochrana životného prostredia',
                'Organizácia práce, príprava náradia a pomôcok',
                'Príprava materiálu na murovanie',
                'Technika murovania – rozprestretie malty, kladenie tehál, väzby tehlového muriva',
                'Zakladanie muriva na základoch a podlažiach',
                'Nosné piliere – technika murovania',
            ],
            10 => [
                'Murovanie – vynechanie otvorov (okná, dvere, drážky)',
                'Ostenia okien a dverí',
                'Kontrola kvality murovania',
                'Murovanie v zime – zabezpečenie prác pri nízkych teplotách',
                'Komíny – BOZP, normy, nástroje a príprava materiálu',
                'Zloženie komínov a úprava komínovej hlavy',
            ],
            11 => [
                'Murovanie a montáž komínov',
                'Kontrola presnosti murovania komínov',
                'Tvarovkové murivo – BOZP, organizácia práce, náradie',
                'Príprava a miešanie malty',
                'Murivo z pórobetónových tvaroviek (postup, presnosť)',
                'Murivo z keramických tvaroviek',
            ],
            12 => [
                'Kontrola presnosti tvarovkového muriva',
                'Kamenné a miešané murivo – BOZP, náradie',
                'Výber a úprava kameňa, výroba malty',
                'Založenie rozmerov a väzieb kamenného múru',
                'Typy kamenného muriva (lomové, kyklopské, riadkové, kvádrové)',
            ],
            1 => [
                'Pokračovanie – typy kamenného muriva',
                'Zmiešané murivo (kameň+tehla, kameň+betón, tehla+betón)',
                'Škárovanie – zalievanie škár a úprava líca',
                'Kontrola kvality kamenného a zmiešaného muriva',
                'Murovanie priečok – BOZP, založenie podľa výkresu',
                'Priečky z plných a dierovaných tehál, dvojité priečky so zvukovou izoláciou',
            ],
            2 => [
                'Prípustné tolerancie, kotvenie priečok do muriva a stropov',
                'Technológia priečok – monolitické, montované, sklenené tvarovky',
                'Sadrokartónové priečky – postup a povrchové úpravy',
                'Osadzovanie zárubní (drevo, oceľ, plast), okien a dverí',
                'Preklady – tehlové, keramické, oceľovo-betónové; kontrola kvality',
            ],
            3 => [
                'BOZP pri omietaní, lešenie, vplyv činností na ŽP',
                'Príprava malty a podkladu (postrek, terče, jadro, hladidlá)',
                'Ručné omietanie – tolerancie a zabezpečenie rovinnosti',
                'Štuková dvojvrstvová omietka, úprava povrchu',
            ],
            4 => [
                'Príprava na fasádne omietky; omietanie vápennou a VC maltou',
                'Farebné úpravy – nátery/striekanie',
                'Šľachtené VC omietky; kontrola kvality vnútorných/vonkajších omietok',
                'Zložitejšie omietky – BOZP, voľba náradia a materiálov',
            ],
            5 => [
                'Vnútorné omietky stien, stropov, pilierov – tolerancie a detaily styku',
                'Pletivá (rabic, keramické), trstina, jutové prvky – bandážovanie',
                'Aktivovaná štuka; stierkové PVA omietky',
                'Zvláštne omietky (perlitová, barytová), škárovanie režného muriva',
                'Novodobé omietky a nástreky; kontrola kvality',
                'Strojové omietanie – BOZP, stroje, príprava materiálu',
            ],
            6 => [
                'Súborné práce – technológia murovania',
                'Súborné práce – tvarovkové, kamenné a zmiešané murivo',
                'Súborné práce – priečky, okenné/dverné otvory',
                'Súborné práce – zložité a strojové omietky',
            ],
        ];

        // 3661 H murár – 3. ročník
        $murar3 = [
            9 => [
                'Izolácie – BOZP, hygiena, náradie a mechanizácia',
                'Výpočet spotreby a príprava materiálu',
                'Vodorovné a zvislé izolácie proti vlhkosti a vode',
                'Tepelná a zvuková izolácia; kontrola kvality',
                'Betónové a oceľobetónové konštrukcie – BOZP; opakovanie ZB',
                'Zbíjanie a montáž dielcov jednoduchého debnenia',
            ],
            10 => [
                'Montáž/demontáž debnení – dielcové, systémové, veľkoplošné, špeciálne',
                'Debnenie prvkov – vynechanie otvorov',
                'Výstuž – výber, meranie, ručné strihanie a ohýbanie',
                'Ukladanie výstuže: základy, zvislé nosné konštrukcie',
                'Monolitické stropy, preklady, vence, schodiská a rampy',
                'Strojné strihanie a ohýbanie – oboznámenie',
            ],
            11 => [
                'Stropy a klenby – BOZP; keramické stropy (technológia)',
                'Novodobé stropy – zhotovenie',
                'Klenutie – podskruženie, klenbové pásy, nadpražia',
                'Klenutie – pätky a klenbové závery; spôsoby murovania (valená, krížová, kopula)',
                'Stabilizácia tvaru klenieb – nadklenbové múriky',
            ],
            12 => [
                'Poruchy klenieb a sanácie – podopretie debnením',
                'Spínanie tiahlami, injektáže, hlboké škárovanie; rabicovanie oblúkov',
                'Rozoberanie a búranie klenieb; kvalita stropov/klenieb',
                'Dokončovacie práce – BOZP, príprava stavby, náradie',
                'Vnútorné obklady – výber a príprava materiálu',
            ],
            1 => [
                'Technologické postupy vnútorných obkladov; kontrola kvality',
                'Vonkajšie obklady – materiál a technologické postupy',
                'Kontrola kvality vonkajších obkladov',
                'Podkladové a nášľapné vrstvy; pokládka dlažieb, škárovanie, čistenie',
                'Špeciálne podlahy; posúdenie kvality',
            ],
            2 => [
                'Lepenie obkladov a dlažieb (betón, SDK, pórobetón) – lepiace zmesi',
                'Sklobetónové konštrukcie – príprava, technológia, kvalita',
                'Sadrokartón – príprava, osadzovanie, povrchové úpravy',
                'Osadzovanie špeciálnych výrobkov a TZB – BOZP',
            ],
            3 => [
                'Malá mechanizácia pri osadzovaní výrobkov',
                'Dodatočné osadzovanie zárubní, rámov, konzol, parapetov, mreží',
                'Osadzovanie štukatérskych prvkov; špecifiká pri pamiatkach',
                'Požiadavky kvality na osadenie výrobkov',
                'Jednoduché prestavby – BOZP, OOPP; podchytenie múrov prekladmi',
            ],
            4 => [
                'Podchytenie múrov oceľovými nosníkmi/prefabrikátmi',
                'Vybúranie otvorov v múre',
                'Dodatočné izolácie proti vlhkosti a tepelné izolácie (postup)',
            ],
            5 => [
                'Dodatočné vybudovanie komínov a opravy',
                'Podmurovanie a podbetónovanie základov',
                'Súborné práce + opakovanie tematických celkov',
            ],
            6 => [
                'Súborné práce, záverečné hodnotenie a skúšky',
            ],
        ];

        // 3675 H maliar – 3. ročník
        $maliar3 = [
            9 => [
                'Úvod – BOZP, PO, hygiena práce a pracoviska',
                'Maľby fasád – BOZP; miešanie farieb, novodobé materiály',
                'Farebné riešenie vonkajších malieb; chyby a opravy',
            ],
            10 => [
                'Výpočet plôch a materiálu; fakturácia',
                'Súborná práca – vonkajšie maľby',
                'Príprava podkladov pod nátery – BOZP',
                'Náradie pre natieračské práce – štetce, valčeky, nové pomôcky',
            ],
            11 => [
                'Údržba a ošetrovanie náradia',
                'Príprava omietok pod nátery',
                'Príprava drevených podkladov',
                'Podklady: betón, sklo, kov – zasiakané/nezasiakané',
                'Výpočet plôch a materiálu; fakturácia',
            ],
            12 => [
                'Technologické postupy náterov – BOZP',
                'Príprava náterov a základné nátery na rôzne podklady',
            ],
            1 => [
                'Výpočty (plochy, spotreba), fakturácia',
                'Súborná práca – základné nátery',
            ],
            2 => [
                'Nátery so špeciálnym určením – BOZP, hygiena, OOPP',
                'Práce na nábytku; ekologické materiály',
                'Syntetické/dvojzložkové, chlórkaučukové, NC nátery',
                'Morenie dreva; fládrovanie; bezpečnostné nátery',
                'Súborná práca – špeciálne nátery',
            ],
            3 => [
                'Zložitejšie techniky – bronzovanie, patinovanie, pozlacovanie',
                'Štukatérske práce',
                'Tapetovanie – BOZP, príprava lepidiel a podkladu',
            ],
            4 => [
                'Výber/príprava tapiet, výpočet spotreby; technika tapetovania, chyby',
                'Súborná práca – tapety',
                'Dekoračné tvarovanie dosiek – výroba, montáž, povrchové úpravy',
                'Príprava a povrchová úprava SDK dosiek',
            ],
            5 => [
                'SDK dosky – príprava a povrchové úpravy',
                'Súborné práce; opakovanie učiva 3. ročníka',
            ],
            6 => [
                'Súborná práca a záverečné hodnotenie/skúšky',
            ],
        ];

        $lines = [];
        if ($has('3661') && $has('murár') && ($has('2') || $has('2.') || $has('2-'))) {
            $lines = $pickMonth($murar2, $monthNo);
        } elseif ($has('3661') && $has('murár') && ($has('3') || $has('3.') || $has('3-'))) {
            $lines = $pickMonth($murar3, $monthNo);
        } elseif ($has('3675') && $has('maliar') && ($has('3') || $has('3.') || $has('3-'))) {
            $lines = $pickMonth($maliar3, $monthNo);
        }

        if (!$lines) return '';
        $lines = array_map(fn($s) => preg_match('~^[-•]~u', $s) ? trim($s) : ('- ' . trim($s)), $lines);
        return implode("\n", $lines);
    }

    /* ========================== DOWNLOAD & PREVIEW ============================ */

    public function actionDownload($filename)
    {
        $path = Yii::getAlias('@backend/web/contracts/' . basename($filename));
        if (!file_exists($path)) {
            throw new NotFoundHttpException('File not found.');
        }
        return Yii::$app->response->sendFile($path);
    }

public function actionPreviewValues()
    {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        $selectedUsers     = Yii::$app->request->post('users', []);
        $formDate          = Yii::$app->request->post('date');
        $partnerId         = Yii::$app->request->post('partner_id');
        $previewUserIdPost = Yii::$app->request->post('preview_user_id');

        if (!$selectedUsers) {
            return ['status' => 'ok', 'data' => []];
        }

        $allUsersText  = $this->buildAllUsersText($selectedUsers);
        $previewUserId = $previewUserIdPost ?: $selectedUsers[0];

        /** @var \common\models\User|null $user */
        $user = \common\models\User::findOne((int)$previewUserId);
        if (!$user) {
            return ['status' => 'ok', 'data' => []];
        }

        // Build data like generation
        $extra = [
            'partner_id' => $partnerId,
            'all_users'  => $allUsersText,
        ];
        $data = $this->buildUserData($user, $formDate, $extra);
        if (!empty($extra)) $data = array_merge($data, $extra);

        // --- Slovak date helpers so date_sk, month_sk, date_words_sk appear in preview ---
        try {
            $dateStr = $formDate ?: ($data['date'] ?? null);
            $dt = $dateStr ? new \DateTime($dateStr) : null;
        } catch (\Throwable $e) { $dt = null; }
        if ($dt) {
            $mNom = [1=>'január', 2=>'február', 3=>'marec', 4=>'apríl', 5=>'máj', 6=>'jún',
                     7=>'júl',    8=>'august',  9=>'september', 10=>'október', 11=>'november', 12=>'december'];
            $mGen = [1=>'januára', 2=>'februára', 3=>'marca', 4=>'apríla', 5=>'mája', 6=>'júna',
                     7=>'júla',    8=>'augusta',  9=>'septembra', 10=>'októbra', 11=>'novembra', 12=>'decembra'];
            $d  = (int)$dt->format('j'); $m = (int)$dt->format('n'); $y = (int)$dt->format('Y');
            $data['date_sk']       = sprintf('%d. %d. %d', $d, $m, $y);
            $data['month_sk']      = $mNom[$m];
            $data['month_sk_gen']  = $mGen[$m];
            $data['date_words_sk'] = sprintf('%d. %s %d', $d, $mGen[$m], $y);
        } else {
            $data['date_sk']       = '';
            $data['month_sk']      = '';
            $data['month_sk_gen']  = '';
            $data['date_words_sk'] = '';
        }

        // --- each_totals for PREVIEW: parse from attendance_monthly ---
        $eachTotals = '';
        if (!empty($data['attendance_monthly'])) {
            $att = str_replace(["\r\n","\r"], "\n", (string)$data['attendance_monthly']);
            $vals = [];
            foreach (explode("\n", $att) as $raw) {
                $raw = trim($raw);
                if ($raw === '') continue;
                $parts = preg_split('/\s*;\s*|\s{2,}|\t/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) < 4) $parts = preg_split('/\s+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) >= 4 && preg_match('/^\d{1,2}:\d{2}$/', $parts[3])) {
                    $vals[] = $parts[3];
                }
            }
            $eachTotals = implode(' ', $vals); // single spaces by default; users can add more in preview
        }
        $data['each_totals'] = $eachTotals;

        // --- Odbor (profession) from study_plan_type_id → study_plan_types.name ---
        if (empty($data['Odbor'])) {
            $name = \common\models\StudyPlanTypes::find()
                ->select('name')
                ->where(['id' => (int)$user->study_plan_type_id])
                ->scalar();
            $data['Odbor'] = $name ? (string)$name : '';
        }

        // mirror aliases so preview works with typo/variant chips too
        $ALIASES = [
            'attendace_monthly' => 'attendance_monthly',
            'mesacna_cinnost'   => 'mesacny_cinnost',
            'odbor'             => 'Odbor', // lowercase alias
        ];
        foreach ($ALIASES as $from => $to) {
            if (!empty($data[$to]) && empty($data[$from])) $data[$from] = $data[$to];
            if (!empty($data[$from]) && empty($data[$to])) $data[$to]   = $data[$from];
        }

        return ['status' => 'ok', 'data' => $data];

    } catch (\Throwable $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
    }



}
