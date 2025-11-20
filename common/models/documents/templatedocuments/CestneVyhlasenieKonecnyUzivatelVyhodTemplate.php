<?php

namespace common\models\documents\templatedocuments;

use Yii;
use common\models\client\Client;
use common\models\client\ClientCompanyInfo;
use common\models\client\ClientPersonalInfo;
use common\models\Template;
use common\models\TemplateZalozenieFirmy;
use Mpdf\Output\Destination;

class CestneVyhlasenieKonecnyUzivatelVyhodTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 507;
    private $_date;
    private $_town;
    private $_client;
    private $_clientPersonalInfo;
    private $_company;

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
                $template = str_replace($field, (new \DateTimeImmutable($this->_date))->format('d.m.Y'), $template);
            }
            if ($field === '[input.miesto_podpisu]') {
                $template = str_replace($field, $this->_town, $template);
            }
            if ($field === '[client.full_name]') {
                $template = str_replace($field, $this->_clientPersonalInfo->getFullName(), $template);
            }
            if ($field === '[client.maiden_name]') {
                $template = str_replace($field, $this->_client->maiden_name, $template);
            }
            if ($field === '[client.birth_date]') {
                $template = str_replace($field, (new \DateTimeImmutable($this->_clientPersonalInfo->birth_date))->format('d.m.Y'), $template);
            }
            if ($field === '[company.full_name]') {
                $template = str_replace($field, $this->_company->getFullName(), $template);
            }
            if ($field === '[company.full_address]') {
                $template = str_replace($field, $this->_company->getFullAddress(), $template);
            }
            if ($field === '[client.full_address]') {
                $template = str_replace($field, $this->_clientPersonalInfo->getFullAddress(), $template);
            }
            if ($field === '[client.citizenship]') {
                $template = str_replace($field, '', $template);
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
        $template = Template::findOne(self::TEMPLATE_ID);

        return $template->content;
    }

    private function getFieldsFromTemplate(): array
    {
        preg_match_all('/\[(.*?)\]/', $this->getTemplate(), $matches);
        return $matches[0];
    }

    public function setDateOfSignature(string $date)
    {
        $this->_date = $date;
        return $this;
    }

    public function setTownOfSignature(string $town)
    {
        $this->_town = $town;
        return $this;
    }

    public function setClientInfo(int $clientId)
    {
        $this->_company = ClientCompanyInfo::find()->where(['=', 'client_id', $clientId])->one();
        $this->_client = Client::findOne(['id' => $clientId]);
        $this->_clientPersonalInfo = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        return $this;
    }

    public function setSelectedClient()
    {
        return $this;
    }

    public function setIncorporationDate($date)
    {
        return $this;
    }
}