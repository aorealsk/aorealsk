<?php

namespace common\models\documents\templatedocuments;

use Yii;
use DateTimeImmutable;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\TemplateZalozenieFirmy;
use common\models\client\ClientPersonalInfo;

class SplnomocnenieZalozenieFirmyTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 477;
    private $date;
    private $town;
    private $konatel;
    private $incorporationDate;
    private $splnomocnenecName;
    private $splnomocnenecAddress;
    private $splnomocnenecSsn;
    private $splnomocnenecBirth;
    private $splnomocnenecDocName;
    private $splnomocnenecDocAddress;
    private $splnomocnenecDocZip;
    private $splnomocnenecDocTown;

    public function create(): void
    {
    }

    public function writeToFile(): void
    {
    }

    /*public function downloadFile(string $content): void
    {
        parent::downloadFile($content);
    }*/

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
            if ($field === '[input.date_of_signature]') {
                $template = str_replace($field, (new DateTimeImmutable($this->date))->format('d.m.Y'), $template);
            } elseif ($field === '[input.miesto_podpisu]') {
                $template = str_replace($field, $this->town, $template);
            } elseif ($field === '[managingdirector_fullname]') {
                $template = str_replace($field, $this->konatel->getFullName(), $template);
            } elseif ($field === '[managingdirector_birth]') {
                $template = str_replace($field, (new DateTimeImmutable($this->konatel->birth_date))->format('d.m.Y'), $template);
            } elseif ($field === '[managingdirector_ssn]') {
                $template = str_replace($field, $this->konatel->ssn, $template);
            } elseif ($field === '[managingdirector_address]') {
                $template = str_replace($field, $this->konatel->address . ', ' . $this->konatel->zip . ', ' .  $this->konatel->town, $template);
            } elseif ($field === '[input.deed_of_incorporation_date_sign]') {
                $template = str_replace($field, (new DateTimeImmutable($this->incorporationDate))->format('d.m.Y'), $template);
            } elseif ($field === '[plenipotentiary_fullname]') {
                $template = str_replace($field, $this->splnomocnenecName, $template);
            } elseif ($field === '[plenipotentiary_ssn]') {
                $template = str_replace($field, $this->splnomocnenecSsn, $template);
            } elseif ($field === '[plenipotentiary_address]') {
                $template = str_replace($field, $this->splnomocnenecAddress, $template);
            } elseif ($field === '[plenipotentiary_correspondence_address]') {
                $template = str_replace($field, $this->splnomocnenecDocAddress, $template);
            } elseif ($field === '[plenipotentiary_correspondence_zip]') {
                $template = str_replace($field, $this->splnomocnenecDocZip, $template);
            } elseif ($field === '[plenipotentiary_correspondence_town]') {
                $template = str_replace($field, $this->splnomocnenecDocTown, $template);
            } elseif ($field === '[plenipotentiary_birth]') {
                $template = str_replace($field, (new DateTimeImmutable($this->splnomocnenecBirth))->format('d.m.Y'), $template);
            } elseif ($field === '[plenipotentiary_doc_name]') {
                $template = str_replace($field, $this->splnomocnenecDocName, $template);
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

    public function setDateOfSignature(string $date)
    {
        $this->date = $date;
        return $this;
    }

    public function setTownOfSignature(string $town)
    {
        $this->town = $town;
        return $this;
    }

    public function setClientInfo(
        int $clientId,
        $splnomocnenecName = null,
        $lawyerName = null,
        $lawyerResidence = null,
        $lawyerIco = null,
        $lawyerRegistrationNumber = null,
        $lawyerEmail = null,
        $serviceProvider = null,
        $splnomocnenecSsn = null,
        $splnomocnenecAddress = null,
        $splnomocnenecBirth = null,
        $splnomocnenecDocName = null,
        $splnomocnenecDocAddress = null,
        $splnomocnenecDocZip = null,
        $splnomocnenecDocTown = null
    ) {
        $this->konatel = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        $this->splnomocnenecName = $splnomocnenecName;
        $this->splnomocnenecSsn = $splnomocnenecSsn;
        $this->splnomocnenecAddress = $splnomocnenecAddress;
        $this->splnomocnenecBirth = $splnomocnenecBirth;
        $this->splnomocnenecDocName = $splnomocnenecDocName;
        $this->splnomocnenecDocAddress = $splnomocnenecDocAddress;
        $this->splnomocnenecDocZip = $splnomocnenecDocZip;
        $this->splnomocnenecDocTown = $splnomocnenecDocTown;
        return $this;
    }

    public function setSelectedClient()
    {
    }

    public function setIncorporationDate($date)
    {
        $this->incorporationDate = $date;
        return $this;
    }
}
