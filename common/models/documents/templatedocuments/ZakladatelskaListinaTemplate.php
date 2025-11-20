<?php
namespace common\models\documents\templatedocuments;

use common\models\{client\ClientCompanyInfo, client\ClientPersonalInfo, Template, TemplateZalozenieFirmy};
use Yii;
use Mpdf\Output\Destination;
use function GuzzleHttp\Psr7\str;

class ZakladatelskaListinaTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 505;
    private $_client = null;
    private $_date = null;
    private $_town = null;
    private $_company = null;

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return (Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one())->name;
    }

    public function process()
    {
        $fields = $this->getFieldsFromTemplate();
        $template = $this->getTemplate();

        foreach ($fields as $field) {
            if ('[client.full_name]' === $field) {
                $template = str_replace($field, $this->_client->getFullName(), $template);
            }
            if ('[client.birth_date]' === $field) {
                $template = str_replace($field, (new \DateTimeImmutable($this->_client->birth_date))->format('d.m.Y'), $template);
            }
            if ('[client.ssn]' === $field) {
                $template = str_replace($field, $this->_client->ssn, $template);
            }
            if ('[client.full_address]' === $field) {
                $template = str_replace($field, $this->_company->address . ', ' . $this->_company->zip . ', ' .  $this->_company->town, $template);
            }
            if ('[client.company_name]' === $field) {
                $template = str_replace($field, $this->_company->name . ' ' . $this->_company->appendix, $template);
            }
            if ('[client.company_fulladdress]' === $field) {
                $template = str_replace($field, $this->_company->address . ', ' . $this->_company->zip . ', ' .  $this->_company->town, $template);
            }
            if ('[client.obchodna_cinnost]' === $field) {
                $template = $this->getListOfBusinesses($template, '[client.obchodna_cinnost]');
            }
            if ('[client.zakladne_imanie]' === $field) {
                $template = str_replace($field, number_format($this->_company->starting_capital,0,'.',' '), $template);
            }
            if ('[client.zi_slovom]' === $field) {
                $template = str_replace($field, $this->_company->starting_capital_text, $template);
            }
            if ('[input.miesto_podpisu]' === $field) {
                $template = str_replace($field, $this->_town, $template);
            }
            if ('[input.date_of_signature]' === $field) {
                $template = str_replace($field, (new \DateTimeImmutable($this->_date))->format('d.m.Y'), $template);
            }
        }

        return $template;
    }

    /**
     * @param string $content
     * @param string $fieldName
     * @return string
     */
    private function getListOfBusinesses(string $content, string $fieldName): string
    {
        $template = '<li>-&nbsp;&nbsp;&nbsp;%s</li>';
        $result = '';
        foreach ($this->_client->businesses as $business) {
            $result .= sprintf($template,$business->subject);
        }
        return str_replace($fieldName, $result, $content);
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

    /**
     * @return mixed|null
     */
    private function getTemplate()
    {
        return (Template::findOne(self::TEMPLATE_ID))->content;
    }

    /**
     * @return mixed
     */
    private function getFieldsFromTemplate()
    {
        preg_match_all('/\[(.*?)\]/', $this->getTemplate(), $matches);
        return $matches[0];
    }

    public function setClientInfo(int $clientId)
    {
        $this->_company = ClientCompanyInfo::find()->where(['=', 'client_id', $clientId])->one();
        $this->_client = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        return $this;
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

    public function setIncorporationDate($date)
    {
      return $this;
    }

    public function setSelectedClient()
    {
        return $this;
    }

}