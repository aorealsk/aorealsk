<?php

namespace common\models\documents\templatedocuments;

use Yii;
use common\models\Office;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\TemplateZalozenieFirmy;

class InformacieOSpracovaniUdajovTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 484;
    private $serviceProvider;
    private $clientId;

    public function create(): void
    {
    }

    public function writeToFile(): void
    {
    }

    public function getTemplateName(): string
    {
        $template = Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one();

        return $template->name;
    }

    public function process()
    {
        $fields = $this->getFieldsFromTemplate();
        $template = $this->getTemplate();

        foreach ($fields as $field) {
            if ($this->serviceProvider && $field === '[service_provider.name]') {
                $template = str_replace($field, $this->serviceProvider->name, $template);
            }
        }

        return $template;
    }

    public function saveFileToDir($content, $filename, $clientId)
    {
        $this->mpdf->WriteHTML($content);

        $template = new TemplateZalozenieFirmy();
        $template->client_id = $clientId;
        $template->pathname = $filename;
        $template->save();

        $this->mpdf->Output(Yii::getAlias('@webroot') . "/../../docs/zalozenie-firmy/" . $filename, Destination::FILE);
    }


    private function getTemplate()
    {
        $template =  Template::findOne(self::TEMPLATE_ID);

        return $template->content;
    }

    private function getFieldsFromTemplate(): array
    {
        preg_match_all('/\[(.*?)\]/', $this->getTemplate(), $matches);
        return $matches[0];
    }

    public function setClientInfo(int $clientId, $personalInfoId = null, $lawyerName = null, $lawyerResidence = null, $lawyerIco = null, $lawyerRegistrationNumber = null, $lawyerEmail = null, $serviceProvider = null)
    {
        $this->clientId = $clientId;
        $this->serviceProvider = Office::findOne($serviceProvider);
        return $this;
    }

    public function setDateOfSignature()
    {
        return $this;
    }

    public function setTownOfSignature()
    {
        return $this;
    }

    public function setSelectedClient()
    {
        return $this;
    }

    public function setIncorporationDate()
    {
        return $this;
    }
}
