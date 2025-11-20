<?php

namespace common\models\documents\templatedocuments;

use Yii;
use DateTimeImmutable;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\TemplateZalozenieFirmy;
use common\models\client\ClientCompanyInfo;
use common\models\client\ClientPersonalInfo;

class SplnomocnenieZalozenieFirmyPravnikTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 481;
    private $date;
    private $town;
    private $companyInfo;
    private $konatel;
    private $incorporationDate;
    private $lawyerName;
    private $lawyerResidence;
    private $lawyerIco;
    private $lawyerRegistrationNumber;

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
            if ($field === '[input.date_of_signature]') {
                $template = str_replace($field, (new DateTimeImmutable($this->date))->format('d.m.Y'), $template);
            } elseif ($field === '[input.miesto_podpisu]') {
                $template = str_replace($field, $this->town, $template);
            } elseif ($field === '[residence. address]') {
                $template = str_replace($field, $this->konatel->address . ', ' . $this->konatel->town . ', ' . $this->konatel->zip, $template);
            } elseif ($field === '[company_name]') {
                $template = str_replace($field, $this->companyInfo->name . ' ' . $this->companyInfo->appendix, $template);
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
            } elseif ($field === '[lawyer_fullname]') {
                $template = str_replace($field, $this->lawyerName, $template);
            } elseif ($field === '[lawyer_residence]') {
                $template = str_replace($field, $this->lawyerResidence, $template);
            } elseif ($field === '[lawyer_ico]') {
                $template = str_replace($field, $this->lawyerIco, $template);
            } elseif ($field === '[lawyer_registration_number]') {
                $template = str_replace($field, $this->lawyerRegistrationNumber, $template);
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

    public function setClientInfo(int $clientId, $personalInfoId = null, $lawyerName = null, $lawyerResidence = null, $lawyerIco = null, $lawyerRegistrationNumber = null)
    {
        $this->companyInfo = ClientCompanyInfo::find()->where(['=', 'client_id', $clientId])->one();
        $this->konatel = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        $this->lawyerName = $lawyerName;
        $this->lawyerResidence = $lawyerResidence;
        $this->lawyerIco = $lawyerIco;
        $this->lawyerRegistrationNumber = $lawyerRegistrationNumber;
        return $this;
    }

    public function setSelectedClient()
    {
        return $this;
    }

    public function setIncorporationDate($date)
    {
        $this->incorporationDate = $date;
        return $this;
    }
}
