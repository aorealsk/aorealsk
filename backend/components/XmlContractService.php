<?php
namespace backend\components;

use setasign\Fpdi\Fpdi;
use Yii;

class XmlContractService
{
    /** Convert UTF-8 to WinAnsi (CP1252) for FPDF core fonts; fallback keeps it safe */
    private function toWinAnsi(string $s): string
    {
        if ($s === '') return '';
        $t = @iconv('UTF-8', 'CP1252//TRANSLIT', $s);
        return $t !== false ? $t : utf8_decode($s);
    }

    /**
     * Render an XML overlay to a PDF.
     *
     * Supported tags:
     *  - <pdf src="..."/>
     *  - <defaults font="Helvetica" size="11" color="#000000"/>
     *  - <field name="..." page="1" x=".." y=".." [size|font|color|width|align]/>
     *  - <text  page="1" x=".." y="..">Static {{placeholder}}</text>
     *  - <repeat name="students" page="2" x=".." y=".." row_h="7" max="17" [font|size|color]>
     *        <col key="#"           dx="0"   align="C"/>
     *        <col key="full_name"   dx="15"  width="90"/>
     *        ...
     *    </repeat>
     *
     * @param string $xmlPath
     * @param array  $data
     * @param string $outputPdf
     * @param bool   $debugGrid  Optional 10mm grid to help positioning
     */
    public function renderToFile(string $xmlPath, array $data, string $outputPdf, bool $debugGrid = false): void
    {
        $xmlPath = Yii::getAlias($xmlPath);
        if (!is_file($xmlPath)) {
            throw new \RuntimeException("XML sablon nem található: $xmlPath");
        }
        $xml = @simplexml_load_file($xmlPath);
        if (!$xml) {
            throw new \RuntimeException("Érvénytelen XML: $xmlPath");
        }

        // --- resolve base PDF (alias / absolute / relative to XML) ---
        $pdfNode = $xml->pdf[0] ?? null;
        if (!$pdfNode || empty($pdfNode['src'])) {
            throw new \RuntimeException('XML-ben hiányzik: <pdf src="...">');
        }
        $src = trim((string)$pdfNode['src']);
        if ($src === '') {
            throw new \RuntimeException('Üres PDF src attribútum.');
        }

        $basePdf = null;

        if ($src[0] === '@') { // alias
            $try = Yii::getAlias($src, false);
            if ($try && is_file($try)) $basePdf = $try;
        }
        if ($basePdf === null && ($src[0] === '/' || preg_match('~^[A-Za-z]:[\\\\/]~', $src))) { // absolute
            if (is_file($src)) $basePdf = $src;
        }
        if ($basePdf === null) { // relative to XML folder
            $try = dirname($xmlPath) . DIRECTORY_SEPARATOR . $src;
            if (is_file($try)) $basePdf = $try;
        }
        if ($basePdf === null) { // extra fallbacks
            $backend = Yii::getAlias('@backend', false);
            if ($backend) {
                $try = $backend . '/templates/contracts/' . ltrim($src, '/');
                if (is_file($try)) $basePdf = $try;
            }
            if ($basePdf === null) {
                $webroot = Yii::getAlias('@webroot', false);
                if ($webroot) {
                    $try = $webroot . '/backend/templates/contracts/' . ltrim($src, '/');
                    if (is_file($try)) $basePdf = $try;
                }
            }
        }

        if ($basePdf === null || !is_file($basePdf)) {
            throw new \RuntimeException('Bázis PDF nem található: ' . ($basePdf ?: $src));
        }

        // --- defaults (use a core font that exists in FPDF) ---
        $defaults = [
            'font'  => (string)($xml->defaults['font']  ?? 'Helvetica'), // Arial/Helvetica/Times are safe
            'size'  => (float) ($xml->defaults['size']  ?? 11),
            'color' => (string)($xml->defaults['color'] ?? '#000000'),
        ];
        $data = array_merge(['today' => date('Y-m-d')], $data);

        // --- render ---
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pageCount = $pdf->setSourceFile($basePdf);

        for ($page = 1; $page <= $pageCount; $page++) {
            $tplIdx = $pdf->importPage($page);
            $size   = $pdf->getTemplateSize($tplIdx);

            $width  = is_array($size) && isset($size['width'])  ? (float)$size['width']  : 210.0;
            $height = is_array($size) && isset($size['height']) ? (float)$size['height'] : 297.0;
            $orient = is_array($size) && isset($size['orientation'])
                ? (string)$size['orientation']
                : ($width > $height ? 'L' : 'P');

            $pdf->AddPage($orient, [$width, $height]);
            $pdf->useTemplate($tplIdx, 0, 0, $width);

            if ($debugGrid) {
                $this->drawGrid($pdf, $width, $height);
            }

            $this->applyColor($pdf, $defaults['color']);
            $pdf->SetFont($defaults['font'], '', $defaults['size']);

            // <field name="..." page="..." x=".." y=".."/>
            foreach ($xml->field as $f) {
                if ((int)$f['page'] !== $page) continue;
                $key = (string)$f['name'];
                $txt = isset($data[$key]) ? (string)$data[$key] : '';
                $this->draw($pdf, $f, $defaults, $txt);
            }

            // <text ...>static {{placeholder}}</text>
            foreach ($xml->text as $t) {
                if ((int)$t['page'] !== $page) continue;
                $raw = (string)$t;
                $txt = preg_replace_callback(
                    '/\{\{(\w+)\}\}/',
                    function ($m) use ($data) { return isset($data[$m[1]]) ? (string)$data[$m[1]] : ''; },
                    $raw
                );
                $this->draw($pdf, $t, $defaults, $txt);
            }

            /* ===== NEW: <repeat> support (simple multi-row tables) ===== */
            foreach ($xml->repeat as $rep) {
                $repPage = (int)$rep['page'];
                if ($repPage !== $page) continue;

                $arrName = (string)$rep['name'];
                $rows    = (array)($data[$arrName] ?? []);
                if (!$rows) continue;

                $x0   = (float)$rep['x'];
                $y0   = (float)$rep['y'];
                $rowH = $rep['row_h'] !== null ? (float)$rep['row_h'] : 7.0;
                $max  = $rep['max']   !== null ? (int)$rep['max']   : PHP_INT_MAX;

                // optional style overrides for the block
                $fontB  = (string)($rep['font']  ?? $defaults['font']);
                $sizeB  = (float) ($rep['size']  ?? $defaults['size']);
                $colorB = (string)($rep['color'] ?? $defaults['color']);

                // read column definitions
                $cols = [];
                foreach ($rep->col as $c) {
                    $cols[] = [
                        'key'   => (string)$c['key'],
                        'dx'    => (float)($c['dx'] ?? 0),
                        'width' => (float)($c['width'] ?? 0),
                        'align' => (string)($c['align'] ?? 'L'),
                    ];
                }
                if (!$cols) continue;

                // set style for block
                $this->applyColor($pdf, $colorB);
                $pdf->SetFont($fontB, '', $sizeB);

                $i = 0;
                foreach ($rows as $idx => $r) {
                    if ($i >= $max) break;
                    $y = $y0 + $i * $rowH;

                    foreach ($cols as $col) {
                        $val = ($col['key'] === '#')
                            ? (string)($i + 1) // 1-based index
                            : (string)($r[$col['key']] ?? '');

                        if ($val === '') continue;

                        // diacritics fix
                        $val = $this->toWinAnsi($val);

                        $pdf->SetXY($x0 + $col['dx'], $y);
                        if ($col['width'] > 0) {
                            $pdf->MultiCell($col['width'], 0, $val, 0, $col['align']);
                        } else {
                            $pdf->Cell(0, 0, $val, 0, 0, $col['align']);
                        }
                    }
                    $i++;
                }
            }
            /* ===== END NEW ===== */
        }

        $pdf->Output($outputPdf, 'F');
    }

    /** Write one value with per-node overrides; convert to WinAnsi for core fonts. */
    private function draw(Fpdi $pdf, \SimpleXMLElement $node, array $defaults, string $text): void
    {
        $x = (float)$node['x'];
        $y = (float)$node['y'];
        $size  = isset($node['size'])  ? (float)$node['size']  : $defaults['size'];
        $color = isset($node['color']) ? (string)$node['color'] : $defaults['color'];
        $font  = isset($node['font'])  ? (string)$node['font']  : $defaults['font'];

        $this->applyColor($pdf, $color);
        $pdf->SetFont($font, '', $size);
        $pdf->SetXY($x, $y);

        // diacritics (á, é, ő, ű, ö, ó, š, č, ž …)
        $text = $this->toWinAnsi($text);

        $width = isset($node['width']) ? (float)$node['width'] : 0.0;
        $align = isset($node['align']) ? (string)$node['align'] : 'L';

        if ($width > 0) {
            $pdf->MultiCell($width, 5, $text, 0, $align);
        } else {
            $pdf->Write(5, $text);
        }
    }

    private function applyColor(Fpdi $pdf, string $hex): void
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) { $pdf->SetTextColor(0,0,0); return; }
        $pdf->SetTextColor(
            hexdec(substr($hex,0,2)),
            hexdec(substr($hex,2,2)),
            hexdec(substr($hex,4,2))
        );
    }

    /** Simple 10 mm positioning grid (optional). */
    private function drawGrid(Fpdi $pdf, float $w, float $h): void
    {
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.1);

        for ($x = 0; $x <= $w; $x += 10) {
            $pdf->Line($x, 0, $x, $h);
            $pdf->SetFont('Helvetica', '', 6);
            $pdf->SetXY($x + 1, 2);
            $pdf->Write(3, (string)$x);
        }

        for ($y = 0; $y <= $h; $y += 10) {
            $pdf->Line(0, $y, $w, $y);
            $pdf->SetFont('Helvetica', '', 6);
            $pdf->SetXY(1, $y + 1);
            $pdf->Write(3, (string)$y);
        }

        $pdf->SetDrawColor(0, 0, 0);
    }
}
