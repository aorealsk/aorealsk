<?php

namespace common\models\documents\templatedocuments;

use Yii;
use DateTimeImmutable;
use common\models\Office;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\TemplateZalozenieFirmy;
use common\models\client\ClientPersonalInfo;

class ZmluvaOPoskitovaniSluziebTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 483;
    private $date;
    private $incorporationDate;
    private $town;
    private $konatel;
    private $lawyerName;
    private $lawyerResidence;
    private $lawyerIco;
    private $lawyerRegistrationNumber;
    private $lawyerEmail;
    private $serviceProvider;
    private $officeIban;
    private $bankName;
    private $odplataCena;
    private $odplataDph;
    private $providerDeputy;

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
            } elseif ($field === '[managingdirector_name]') {
                $template = str_replace($field, $this->konatel->getFullName(), $template);
            } elseif ($field === '[managingdirector_birth]') {
                $template = str_replace($field, (new DateTimeImmutable($this->konatel->birth_date))->format('d.m.Y'), $template);
            } elseif ($field === '[managingdirector_ssn]') {
                $template = str_replace($field, $this->konatel->ssn, $template);
            } elseif ($field === '[managingdirector_address]') {
                $template = str_replace($field, $this->konatel->address . ', ' . $this->konatel->zip . ', ' .  $this->konatel->town, $template);
            } elseif ($field === '[lawyer_fullname]') {
                $template = str_replace($field, $this->lawyerName, $template);
            } elseif ($field === '[lawyer_residence]') {
                $template = str_replace($field, $this->lawyerResidence, $template);
            } elseif ($field === '[lawyer_ico]') {
                $template = str_replace($field, $this->lawyerIco, $template);
            } elseif ($field === '[lawyer_registration_number]') {
                $template = str_replace($field, $this->lawyerRegistrationNumber, $template);
            } elseif ($field === '[lawyer_email]') {
                $template = str_replace($field, $this->lawyerEmail, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider.name]') {
                $template = str_replace($field, $this->serviceProvider->name, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider.address]') {
                $template = str_replace($field, $this->serviceProvider->address, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider.town]') {
                $template = str_replace($field, $this->serviceProvider->town, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider.zip]') {
                $template = str_replace($field, $this->serviceProvider->zip, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider.ico]') {
                $template = str_replace($field, $this->serviceProvider->ico, $template);
            } elseif ($this->serviceProvider && $field === '[service_provider_registered]') {
                $template = str_replace($field, $this->serviceProvider->registered, $template);
            } elseif ($this->serviceProvider && $field === '[office.email]') {
                $template = str_replace($field, $this->serviceProvider->email, $template);
            } elseif ($this->officeIban != '' && $field === '[office_bank_account]') {
                $template = str_replace($field, $this->officeIban, $template);
            } elseif ($this->bankName != '' && $field === '[office_bank_account_name]') {
                $template = str_replace($field, $this->bankName, $template);
            } elseif ($this->odplataCena != '' && $field === '[odplata_cena]') {
                $template = str_replace($field, $this->odplataCena, $template);
            } elseif ($this->odplataDph != '' && $field === '[odplata_dph]') {
                $template = str_replace($field, $this->odplataDph, $template);
            } elseif ($this->providerDeputy != '' && $field === '[service_provider.deputy]' ) {
                $template = str_replace($field, $this->providerDeputy, $template);
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
        $splnomocnenecDocTown = null,
        $officeIban = null,
        $bankName = null,
        $odplataCena = null,
        $odplataDph = null,
        $providerDeputy = null
    ) {
        $this->konatel = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        $this->lawyerName = $lawyerName;
        $this->lawyerEmail = $lawyerEmail;
        $this->lawyerResidence = $lawyerResidence;
        $this->lawyerIco = $lawyerIco;
        $this->lawyerRegistrationNumber = $lawyerRegistrationNumber;
        $this->serviceProvider = Office::findOne($serviceProvider);
        $this->officeIban = $officeIban;
        $this->bankName = $bankName;
        $this->odplataCena = $odplataCena;
        $this->odplataDph = $odplataDph;
        $this->providerDeputy = $providerDeputy;
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
