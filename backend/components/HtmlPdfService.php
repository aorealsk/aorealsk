<?php
namespace backend\components;

use Yii;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class HtmlPdfService
{
    public function renderToFile(string $html, string $outputPdf): void
    {
        $dir = dirname($outputPdf);
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 10,
            // shared hostokon fontos, hogy írható temp legyen:
            'tempDir'       => Yii::getAlias('@runtime/mpdf'),
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output($outputPdf, Destination::FILE);
    }
}
