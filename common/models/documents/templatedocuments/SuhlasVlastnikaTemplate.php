<?php

namespace common\models\documents\templatedocuments;

use Yii;
use DateTimeImmutable;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\client\ClientCompanyLv;
use common\models\TemplateZalozenieFirmy;
use common\models\client\ClientCompanyInfo;

class SuhlasVlastnikaTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 476;
    private $date;
    private $town;
    private $companyInfo;
    private $propertyList;

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
            } elseif ($field === '[company_name]') {
                $template = str_replace($field, $this->companyInfo->name . ' ' . $this->companyInfo->appendix, $template);
            } elseif ($field === '[company_residence]') {
                $template = str_replace($field, $this->companyInfo->address . ', ' . $this->companyInfo->town . ', ' . $this->companyInfo->zip, $template);
            } elseif ($field === '[nehnutelnost_town]') {
                $template = str_replace($field, $this->companyInfo->town, $template);
            } elseif ($field === '[nehnutelnost_address]') {
                $template = str_replace($field, $this->companyInfo->address, $template);
            } elseif ($field === '[nehnutelnost_kat_uzemie]') {
                $template = str_replace($field, $this->companyInfo->cadastral_area, $template);
            }
            foreach ($this->propertyList->owners as $i => $owner) {
                if ($field === "[predaj_op_fullname$i]") {
                    $template = str_replace($field, $owner->first_name . ' ' . $owner->last_name, $template);
                } elseif ($field === "[predaj_op_address$i]") {
                    $template = str_replace($field, $owner->address . ', ' . $owner->zip . ', ' . $owner->town, $template);
                } elseif ($field === "[predaj_op_birth$i]") {
                    $template = str_replace($field, (new DateTimeImmutable($owner->birth_date))->format('d.m.Y'), $template);
                } elseif ($field === '[nehnutelnost_cislo_list_vlast]') {
                    $template = str_replace($field, $this->propertyList->lv_number, $template);
                }
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
        return $this->addAdditionalFields($template->content);
    }

    private function getFieldsFromTemplate(): array
    {
        preg_match_all('/\[(.*?)\]/', $this->getTemplate(), $matches);
        return $matches[0];
    }

    private function addAdditionalFields(string $content): string
    {
        $content = str_replace('[block.suhlas_majitel_detaily|loop:1..n]', $this->getAdditionalFields(), $content);
        $content = str_replace('[owners_signature]', $this->getAdditionalSignatures(), $content);
        return $content;
    }

    private function getAdditionalFields(): string
    {
        $content = '';
        foreach ($this->propertyList->owners as $i => $owner) {
            $content .= nl2br(
                "
            Meno a priezvisko: [predaj_op_fullname$i] \n
            Bytom: [predaj_op_address$i] \n
            Narodený: [predaj_op_birth$i] \n"
            );
        }
        return $content;
    }

    private function getAdditionalSignatures(): string
    {
        $content = '';
        foreach ($this->propertyList->owners as $i => $owner) {
            $content .= "
            <p class='text' style='margin-top:35px;'>
                ..........................................................................
            </p>
            <p class='text'>
                [predaj_op_fullname$i]
            </p>";
        }
        return $content;
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

    public function setClientInfo(int $clientId)
    {
        $this->companyInfo = ClientCompanyInfo::find()->where(['=', 'client_id', $clientId])->one();
        $this->propertyList = ClientCompanyLv::find()->where(['=', 'client_id', $clientId])->one();
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
