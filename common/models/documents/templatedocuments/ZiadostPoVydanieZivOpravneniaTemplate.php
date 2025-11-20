<?php
namespace common\models\documents\templatedocuments;

/*
 * FORMULÁR pre právnickú osobu
 * ohlásenie / žiadosť  o vydanie osvedčenia o živnostenskom oprávnení
 */

use common\helpers\StringHelper;
use common\models\client\Client;
use common\models\client\ClientCompanyInfo;
use common\models\client\ClientContact;
use common\models\client\ClientPersonalInfo;
use common\models\Okres;
use Yii;
use common\models\Template;
use common\models\TemplateZalozenieFirmy;
use Mpdf\Output\Destination;


class ZiadostPoVydanieZivOpravneniaTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 506;
    const PAGE_BREAK = '[pagebreak]';

    private $_clientInfo = null;
    private $_company = null;
    private $_client = null;
    private $_clientContact = null;

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
        $content = $this->getTemplate();

        foreach ($fields as $field) {
            if (self::PAGE_BREAK === $field) {
                continue;
            }
            if ('[company.name]' === $field) {
                $content = str_replace( $field, $this->_company->name, $content);
            }
            if ('[company.legal_form]' === $field) {
                $content = str_replace( $field, $this->_company->appendix, $content);
            }
            if ('[company.country]' === $field) {
                $content = str_replace ($field, $this->_company->country->iso_kod, $content);
            }
            if ('[company.address]' === $field) {
                $content = str_replace($field, $this->_company->street_name, $content);
            }
            if ('[company.orientacne_supisne_cislo]' === $field) {
                $content = str_replace($field, $this->_company->property_number, $content);
            }
            if ('[company.ico]' === $field) {

            }
            if ('[client.ssn]' === $field) {
                $content = str_replace($field, $this->_clientInfo->ssn, $content);
            }
            if ('[client.first_name]' === $field) {
                $content = str_replace($field, $this->_clientInfo->first_name, $content);
            }
            if ('[client.last_name]' === $field) {
                $content = str_replace($field, $this->_clientInfo->last_name, $content);
            }
            if ('[client.town]' === $field) {
                $content = str_replace($field, $this->_clientInfo->town, $content);
            }
            if ('[client.zip]' === $field) {
                $content = str_replace($field, $this->_clientInfo->zip, $content);
            }
            if ('[company.zip]' === $field) {
                $content = str_replace($field, $this->_company->zip, $content);
            }
            if ('[company.town]' === $field) {
                $content = str_replace($field, $this->_company->town, $content);
            }
            if ('[client.ac_deg_before]' === $field) {
                $content = str_replace($field, $this->_clientInfo->adegree_before ?? '', $content);
            }
            if ('[client.ac_deg_after]' === $field) {
                $content = str_replace($field, $this->_clientInfo->adegree_after ?? '', $content);
            }
            if ('[client.birth_date]' === $field) {
                $content = str_replace(
                    $field,
                    (new \DateTimeImmutable($this->_clientInfo->birth_date))
                        ->format('d.m.Y'),
                    $content);
            }

        }
        return $content;
    }

    protected function processPageBrake(string $content)
    {
        $pos = strpos( $content, self::PAGE_BREAK, 0);

        if ($pos !== FALSE) {
            $subString = substr($content,0, $pos + strlen(self::PAGE_BREAK));
            $subString = str_replace(self::PAGE_BREAK, '', $subString);
            $this->mpdf->WriteHTML($subString);
            $this->mpdf->AddPage();
            $this->processPageBrake(substr($content, $pos + strlen(self::PAGE_BREAK)));
        }
        $this->mpdf->WriteHTML($content);
    }

    public function saveFileToDir($content, $filename, $clientId)
    {

        //if ($this->_hasPageBreak) {
        $this->processPageBrake($content);
        //} else {
        //    $this->mpdf->WriteHTML($content);
        //}

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
        $this->_clientInfo = ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->andWhere(['=', 'client_type', 'konatel'])->one();
        $this->_client = Client::findOne(['id'=>$clientId]);
        $this->_clientContact = ClientContact::find()->where(['=','client_id',$clientId])->one();
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

    /**
     * @return $this
     */
    public function enablePageNumbering(string $align = 'right')
    {
        //$this->mpdf->SetHTMLFooter("<div style=\"text-align: {$align}; font-family: inherit; font-size: inherit\">{PAGENO}</div>");
        $this->mpdf->SetHTMLFooter("
            <div>T MV SR 2007/61-{PAGENO}</div>
        ");
        return $this;
    }

    private function getRegions()
    {
        $regions = Okres::find()->select('name')->asArray()->all();
        $items=[];
        foreach ($regions as $region) {
            $items[] = sprintf('<option value="%s">%s</option>', $region['name'], $region['name']);
        }
        return $items;
    }

    public function viewTemplate()
    {
        $fields = $this->getFieldsFromTemplate();
        $content = $this->getTemplate();
        $replacement = '<br><input type="text" data-item="%s" class="doc-item" value="%s" style="margin: 5px;">';
        $replacementNumber = '<br><input type="number" data-item="%s" class="doc-item" style="margin: 5px">';
        $replacementTextArea = '<br><textarea data-item="%s" class="doc-item" style="margin: 5px; width: 98%%">%s</textarea>';
        $replacementSelect = '<br><select data-item="%s" class="doc-item" style="margin:5px">%s</select>';

        foreach ($fields as $field) {
            $item = $field;
            if (self::PAGE_BREAK === $field) {
                continue;
            }
            if ('[okresny_urad]' === $field) {
                $items = $this->getRegions();
                $item = sprintf($replacementSelect, trim($field,'[]'), implode('', $items));
            }
            if ('[input.k_predmetom_podnikania]' === $field) {
                $item = sprintf($replacementTextArea, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.citizenship]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.birth_date]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.ssn]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.family_name]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.ac_deg_after]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.ac_deg_before]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.first_name]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[zodpovedna_osoba.last_name]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[input.sposobilost]' === $field) {
                $item = sprintf($replacementTextArea, trim($field, '[]'), $field);
            }
            if ('[client.okres]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $field);
            }
            if ('[client.ico]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $field);
            }
            if ('[client.citizenship]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $field);
            }
            if ('[company.name]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_company->name);
            }
            if ('[company.legal_form]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_company->appendix);
            }
            if ('[company.country]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_company->country->iso_kod);
            }
            if ('[company.address]' === $field) {
                $item = sprintf($replacement,trim($field,'[]'), $this->_company->street_name);
            }
            if ('[company.orientacne_supisne_cislo]' === $field) {
                $item = sprintf($replacement,trim($field,'[]'), $this->_company->property_number);
            }
            if ('[company.ico]' === $field) {
                $item= sprintf($replacement, trim($field,'[]'), $field);
            }
            if ('[company.okres]' === $field) {
                $items = $this->getRegions();
                $item = sprintf($replacementSelect, trim($field,'[]'), implode('',$items));
            }
            if ('[company.phone]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $this->_clientContact->getFullMobileNumber());
            }
            if ('[client.birth_name]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $this->_client->maiden_name);
            }
            if ('[client.ssn]' === $field) {
                $item = sprintf($replacement,trim($field,'[]'), $this->_clientInfo->ssn);
            }
            if ('[client.first_name]' === $field) {
                $item = sprintf($replacement,trim($field,'[]'), $this->_clientInfo->first_name);
            }
            if ('[client.last_name]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_clientInfo->last_name);
            }
            if ('[client.town]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_clientInfo->town);
            }
            if ('[client.zip]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_clientInfo->zip);
            }
            if ('[company.zip]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_company->zip);
            }
            if ('[company.town]' === $field) {
                $item = sprintf($replacement,trim($field,'[]'),  $this->_company->town);
            }
            if ('[client.ac_deg_before]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_clientInfo->adegree_before ?? '');
            }
            if ('[client.ac_deg_after]' === $field) {
                $item = sprintf($replacement, trim($field,'[]'), $this->_clientInfo->adegree_after ?? '');
            }
            if ('[client.birth_date]' === $field) {
                $item = sprintf(
                    $replacement,
                    trim($field,'[]'),
                    (new \DateTimeImmutable($this->_clientInfo->birth_date))
                        ->format('d.m.Y'));
            }
            if ('[company.email]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $this->_client->email);
            }
            if ('[client.country]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $field);
            }
            if ('[client.address]' === $field) {
                $item = sprintf($replacement, trim($field, '[]'), $field);
            }
            if ('[client.supisne_orientacne_cislo]' === $field) {
                $item = sprintf($replacement, trim($field, ']['), $field);
            }
            if (\yii\helpers\StringHelper::startsWith(trim($field,'[]'),'input.por_cislo')){
                $item = sprintf($replacementNumber, trim($field,'[]'),$field);
            }
            if ( \yii\helpers\StringHelper::startsWith(trim($field,'[]'),'input.priloha')) {
                $item = sprintf($replacement, trim($field,'[]'),$field);
            }
            $content = str_replace( $field, $item, $content);
        }
        return $content;
    }

}