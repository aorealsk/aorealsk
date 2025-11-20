<?php
namespace backend\components;

use Yii;

// make sure FPDF is available (some hosts don't autoload it)
if (!class_exists('\FPDF')) {
    $fpdfPath = Yii::getAlias('@vendor/setasign/fpdf/fpdf.php');
    if (is_file($fpdfPath)) {
        require_once $fpdfPath;
    }
}

use setasign\Fpdi\Fpdi;

class PdfOverlayService
{
    /** Convert UTF-8 → WinAnsi (CP1252) for FPDF core fonts; fall back to utf8_decode */
    private function toWinAnsi(string $s): string
    {
        if ($s === '') return '';
        $t = @iconv('UTF-8', 'CP1252//TRANSLIT', $s);
        return $t !== false ? $t : utf8_decode($s);
    }

    /**
     * @param string $templatePdf  Absolute path to base PDF
     * @param array  $fields       ['key' => 'value']
     * @param array  $map          ['key' => ['page'=>int,'x'=>mm,'y'=>mm,'size'=>int,'bold'=>bool,'width'=>mm,'align'=>'L|C|R']]
     * @param string $outputPdf    Absolute path to output file
     * @param bool   $debugGrid    Draw 10 mm grid if true
     */
    public function fill(string $templatePdf, array $fields, array $map, string $outputPdf, bool $debugGrid = false): void
    {
        if (!is_file($templatePdf) || !is_readable($templatePdf)) {
            throw new \RuntimeException('Template PDF nem található vagy nem olvasható: ' . $templatePdf);
        }

        $outDir = dirname($outputPdf);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($templatePdf);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $size  = $pdf->getTemplateSize($tplId);

            $width  = is_array($size) && isset($size['width'])  ? (float)$size['width']  : (float)$size[0];
            $height = is_array($size) && isset($size['height']) ? (float)$size['height'] : (float)$size[1];
            $orient = is_array($size) && isset($size['orientation'])
                ? (string)$size['orientation']
                : ($width > $height ? 'L' : 'P');

            $pdf->AddPage($orient, [$width, $height]);
            $pdf->useTemplate($tplId, 0, 0, $width, $height, true);

            if ($debugGrid) {
                $this->drawGrid($pdf, $width, $height);
            }

            foreach ($map as $key => $cfg) {
                $cfgPage = isset($cfg['page']) ? (int)$cfg['page'] : 1;
                if ($cfgPage !== $pageNo) {
                    continue;
                }

                // 🔤 fix diacritics: convert UTF-8 to WinAnsi for FPDF
                $val = isset($fields[$key]) ? $this->toWinAnsi(trim((string)$fields[$key])) : '';
                if ($val === '') {
                    continue;
                }

                $sizePt = isset($cfg['size']) ? (int)$cfg['size'] : 11;
                $bold   = !empty($cfg['bold']);
                $widthC = isset($cfg['width']) ? (float)$cfg['width'] : 0.0; // 0 => unlimited
                $align  = isset($cfg['align']) ? (string)$cfg['align'] : 'L';
                $x      = isset($cfg['x']) ? (float)$cfg['x'] : 0.0;
                $y      = isset($cfg['y']) ? (float)$cfg['y'] : 0.0;

                $pdf->SetFont('Helvetica', $bold ? 'B' : '', $sizePt);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY($x, $y);

                if ($widthC > 0) {
                    $pdf->MultiCell($widthC, 5, $val, 0, $align);
                } else {
                    $pdf->Write(5, $val);
                }
            }
        }

        $pdf->Output('F', $outputPdf);
    }

    /** Simple 10 mm positioning grid. */
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
