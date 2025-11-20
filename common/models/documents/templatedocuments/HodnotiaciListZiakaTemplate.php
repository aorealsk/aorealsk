<?php

namespace common\models\documents\templatedocuments;

use backend\helpers\HelperString;
use common\helpers\DateHelper;
use Yii;
use DateTimeImmutable;
use common\models\User;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\schools\School;
use common\models\schools\Students;

class HodnotiaciListZiakaTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 15;

    private $student;
    private $month;
    private $dateInput;
    private $instructor;
    private $odborneHodnotenieZnamka;
    private $poznamkyKCinnostiamOdborne;
    private $praktickeHodnotenieZnamka;
    private $poznamkyKCinnostiamPrakticke;
    private $kvalitaPrace;
    private $kvalitaPraceInput;
    private $pokyny;
    private $pokynyInput;
    private $povinosti;
    private $povinostiInput;
    private $bozp;
    private $bozpInput;
    private $samostatnost;
    private $samostatnostInput;
    private $poznamky;

    public function create(): void
    {
    }

    public function writeToFile(): void
    {
    }

    public function getFileName(): string
    {
        $template = Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one();
        return iconv('UTF-8', 'ASCII//TRANSLIT', str_replace([" ","/"],"_",$template->name."_".$this->student->getFullName().".pdf"));
    }

    public function getExportName(): string
    {
        $template = Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one();

        return $template->export_name;
    }

    public function process()
    {
        $school = School::find()->where(['=', 'id', $this->student->schoolId])->one();
        $template = $this->getTemplate(self::TEMPLATE_ID);
        $fields = $this->getContentFromTemplate($template['content']);

        foreach ($fields as $field) {
            if ($field === '[school.address]') {
                $template['content'] = str_replace($field, $school->address, $template['content']);
            }
            if ($field === '[school.town]') {
                $template['content'] = str_replace($field, $school->town, $template['content']);
            }
            if ($field == '[school.name]') {
                $template['content'] = str_replace($field, $school->description, $template['content']);
            }
            if ($field == '[month]') {
                $template['content'] = str_replace($field, DateHelper::getMonthText($this->month), $template['content']);
            }
            if ($field == '[trainee.full_name]') {
                $template['content'] = str_replace($field, $this->student->getFullName(), $template['content']);
            }
            if ($field == '[input.date]') {
                $template['content'] = str_replace($field, (new DateTimeImmutable($this->dateInput))->format('d.m.Y'), $template['content']);
            }
            if ($field == '[OdborneVedomostiInput]') {
                $template['content'] = str_replace($field, $this->poznamkyKCinnostiamOdborne, $template['content']);
            }
            if ($field == '[PraktickeVedomostiInput]') {
                $template['content'] = str_replace($field, $this->poznamkyKCinnostiamPrakticke, $template['content']);
            }
            if ($this->instructor && $field == '[instructor.full_name]') {
                $template['content'] = str_replace($field, $this->instructor, $template['content']);
            }
            if ($field == '[PovinostiInput]') {
                $template['content'] = str_replace($field, $this->povinostiInput, $template['content']);
            }
            if ($field == '[PokynyInput]') {
                $template['content'] = str_replace($field, $this->pokynyInput, $template['content']);
            }
            if ($field == '[BOZPInput]') {
                $template['content'] = str_replace($field, $this->bozpInput, $template['content']);
            }
            if ($field == '[KvalitaPraceInput]') {
                $template['content'] = str_replace($field, $this->kvalitaPraceInput, $template['content']);
            }
            if ($field == '[SamostatnostInput]') {
                $template['content'] = str_replace($field, $this->samostatnostInput, $template['content']);
            }
            if ($field == '[poznamky]') {
                $template['content'] = str_replace($field, $this->poznamky, $template['content']);
            }
        }
        for ($i = 1; $i < 6; $i++) {
            $template['content'] = str_replace("[OV$i]", $this->odborneHodnotenieZnamka == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[PV$i]", $this->praktickeHodnotenieZnamka == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[PO$i]", $this->povinosti == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[PK$i]", $this->pokyny == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[BP$i]", $this->bozp == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[KP$i]", $this->kvalitaPrace == $i ? 'X' : '', $template['content']);
            $template['content'] = str_replace("[SA$i]", $this->samostatnost == $i ? 'X' : '', $template['content']);
        }
        return $template['content'];
    }

    public function saveFileToDir($content, $filename)
    {
        $this->mpdf->WriteHTML($content);
        $this->mpdf->Output(Yii::getAlias('@webroot') . "/../docstore/skola/" . $filename, Destination::FILE);
    }

    private function getContentFromTemplate($content)
    {
        preg_match_all('/\[(.*?)\]/', $content, $matches);
        return $matches[0];
    }

    private function getTemplate($templateId)
    {
        return Template::find()->where(['=', 'id', $templateId])->asArray()->one();
    }

    public function setUser(User $user)
    {
        $this->student = Students::find()->where(['=', 'email', $user->email])->one();
        return $this;
    }

    public function setMonth(string $month)
    {
        $this->month = $month;
        return $this;
    }

    public function setDateInput(string $dateInput)
    {
        $this->dateInput = $dateInput;
        return $this;
    }

    public function setInputs($data)
    {
        $this->instructor = $data['instructor'];
        $this->poznamky = $data['poznamky'];

        $this->odborneHodnotenieZnamka =  $data['odborneVedomostiZnamka'];
        $this->poznamkyKCinnostiamOdborne = $data['odborneVedomostiInput'];

        $this->praktickeHodnotenieZnamka = $data['praktickeVedomostiZnamka'];
        $this->poznamkyKCinnostiamPrakticke = $data['praktickeVedomostiInput'];

        $this->kvalitaPrace = $data['kvalitaPrace'];
        $this->kvalitaPraceInput = $data['kvalitaPraceInput'];

        $this->pokyny = $data['pokyny'];
        $this->pokynyInput = $data['pokynyInput'];

        $this->povinosti = $data['povinosti'];
        $this->povinostiInput = $data['povinostiInput'];

        $this->bozp = $data['bozp'];
        $this->bozpInput = $data['bozpInput'];

        $this->samostatnost = $data['samostatnost'];
        $this->samostatnostInput = $data['samostatnostInput'];
    }
}
