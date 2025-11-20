<?php
namespace common\models\documents\templatedocuments;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use yii\helpers\StringHelper;
use Yii;

class PdfTemplateDocument extends TemplateDocument
{
    const PAGE_NUMBER_LEFT = 'left';
    const PAGE_NUMBER_CENTER = 'center';
    const PAGE_NUMBER_RIGHT = 'right';

    protected $mpdf = null;
    protected $documentProtection = false;
    private $orientation = 'P';
    protected $content = null;

    public function __construct(bool $withWaterMarks = false)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        try {
            $this->mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    __DIR__ . "/../../../backend/templates/font",
                ]),
                'fontdata' => $fontData + [
                        'copperplate' => [
                            'R' => 'Copperplate Gothic Bold Regular.ttf',
                            'I' => 'Copperplate Gothic Bold Regular.ttf',
                            'B' => 'Copperplate Gothic Bold Regular.ttf',
                        ]
                    ],
                'default_font' => 'Arial Narrow',
                'orientation' => $this->orientation
            ]);
            $this->mpdf->SetWaterMarkImage(
                Yii::getAlias('@webroot'). '/images/watermark.png', 0.1, 'P'
            );
            //$this->mpdf->showWatermarkImage = true;
            $this->mpdf->SetProtection(
                array('print','annot-forms', 'fill-forms', 'fill-forms', 'print-highres'), '', Yii::$app->params['documentPassword']
            );
        } catch(MpdfException $e) {
            echo $e->getMessage();
            exit;
        }

        if ($this->documentProtection) {
            $this->mpdf->SetProtection([
                'print',
                'print-highres'
            ]);
        }

    }

    public function create(): void
    {
        foreach($this->templateData as $key => $item) {
            if (is_array($item)) {
                $templateCount = count($item);
                foreach($item as $r) {
                    $this->processTemplateData($r, $key);
                    --$templateCount;
                    if (0 != $templateCount) {
                        $this->templateContent .= $this->templateContentStore;
                    }
                }
            } else {
                $this->processTemplateData($item, $key);
            }
        }
    }

    private function processTemplateData($item, $key): void
    {
        if ($item instanceof \yii\db\ActiveRecord) {
            foreach($item->attributes() as $attr) {
                $this->templateContent = str_replace("[{$key}.{$attr}]",$item->$attr,$this->templateContent);
            }
        }
        if (is_string($item) && StringHelper::startsWith($key,'input.')) {
            $this->templateContent = str_replace("[{$key}]",$item,$this->templateContent);
        }
    }

    public function writeToFile(): void
    {
        $this->fileName = "print-" . uniqid() . ".pdf";
        try{
            $this->mpdf->WriteHTML($this->templateContent);
        } catch(\Mpdf\MpdfException $ex) {
            echo $ex->getLine();
            print_r($ex->getTrace());
            exit;
        }
        $this->mpdf->Output(Yii::getAlias('@webroot')."/../docstore/offers/".$this->fileName, Destination::FILE);
    }

    protected function processPageBrake(string $content)
    {
        $this->mpdf->WriteHTML($content);
    }

    public function downloadFile(string $content): void
    {
        //$this->fileName = $this->fileName . time() . ".pdf" ?? "print-" . time() . ".pdf";
        $this->fileName = iconv('UTF-8',
            'ASCII//TRANSLIT',
            str_replace([" ","/"],"_",$this->getTemplateName()."_".time().".pdf"));
        try{
            $this->processPageBrake($content);
            $this->mpdf->Output($this->fileName, Destination::DOWNLOAD);
        } catch(\Mpdf\MpdfException $ex) {
            echo $ex->getLine();
            print_r($ex->getTrace());
            exit;
        }
    }

    public function setOrientation(string $orientation):void
    {
        $this->orientation = $orientation;
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function enableWaterMark(bool $visible = false)
    {
        $this->mpdf->showWatermarkImage = $visible;
        return $this;
    }

    /**
     * @return $this
     */
    public function enablePageNumbering(string $align = 'right')
    {
        $this->mpdf->SetHTMLFooter("<div style=\"text-align: {$align}; font-family: inherit; font-size: inherit\">{PAGENO}</div>");
        return $this;
    }
}