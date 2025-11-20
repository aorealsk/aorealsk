<?php

namespace common\models\documents\templatedocuments;

use common\models\users\UserAttendance;
use Yii;
use DateTime;
use common\models\User;
use common\models\Osnova;
use common\models\Template;
use Mpdf\Output\Destination;
use common\helpers\DateHelper;
use common\helpers\TimeHelper;
use common\models\schools\School;
use common\models\schools\Students;

class MesacnyVykazTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 12;

    private $student;
    private $month;
    private $znamka;
    private $_user;
    private $employerName;
    private $employerAddress;
    private $datum;
    private $od;
    private $do;
    private $cinnost;
    private $hodiny;
    private $odpracovane;

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
        $osnovy = $this->getMonthsFromSyllabus();
        $template = $this->getTemplate(self::TEMPLATE_ID);
        $fields = $this->getFieldsFromTemplate($template['content']);

        foreach ($fields as $field) {
            if ($field == '[school.name]') {
                $template['content'] = str_replace($field, $school->description, $template['content']);
            }
            if ($field == '[school.address]') {
                $template['content'] = str_replace($field, $school->address, $template['content']);
            }
            if ($field == '[school.town]') {
                $template['content'] = str_replace($field, $school->town, $template['content']);
            }
            if ($field == '[school.full_address]') {
                $template['content'] = str_replace($field, "{$school->address}, {$school->zip} {$school->town}", $template['content']);
            }
            if ($field == '[month]') {
                $template['content'] = str_replace($field, DateHelper::getMonthText($this->month), $template['content']);
            }
            if ($field == '[employer.name]') {
                $template['content'] = str_replace($field, $this->employerName, $template['content']);
            }
            if ($field == '[trainee.full_name]') {
                $template['content'] = str_replace($field, $this->student->getFullName(), $template['content']);
            }
            if ($this->znamka != null && $field == '[input.znamka]') {
                $template['content'] = str_replace($field, $this->znamka, $template['content']);
            }
            if ($field === '[employer.address]') {
                $template['content'] = str_replace($field, $this->employerAddress, $template['content']);
            }

            for ($i=0; $i<4; $i++) {
                $template['content'] = str_replace("[datum{$i}]", (new DateTime($this->datum[$i]))->format('d.m.Y'), $template['content']);
                $template['content'] = str_replace("[od{$i}]", $this->od[$i], $template['content']);
                $template['content'] = str_replace("[do{$i}]", $this->do[$i], $template['content']);
                $template['content'] = str_replace("[hodiny{$i}]", $this->odpracovane[$i], $template['content']);
                $template['content'] = str_replace("[cinnost{$i}]", $this->cinnost[$i], $template['content']);
            }
            $template['content'] = str_replace("[hodiny]", $this->hodiny, $template['content']);
            /*$hoursSum = 0;
            foreach ($osnovy as $i => $osnova) {
                $timediff = $this->differenceInHours($osnova['date'], $osnova['od'], $osnova['do']);
                $odpracovane = (UserAttendance::find()
                    ->andWhere(['=','uaDate',$osnova['date']])
                    ->andWhere(['=','userId',$this->_user->getId()])
                    ->andWhere(['=','uaType',UserAttendance::REGULAR_WORKTIME])
                    ->one() == false) ? 0 : 1;
                $hoursSum += (float)$timediff['decimalFormat'] * $odpracovane ;

                if ($field == "[datum" . $i . "]") {
                    $template['content'] = str_replace($field, (new DateTime($osnova['date']))->format('d.m.Y'), $template['content']);
                }
                if ($field == "[od" . $i . "]") {
                    $template['content'] = str_replace($field, (new DateTime($osnova['od']))->format("H:i"), $template['content']);
                }
                if ($field == "[do" . $i . "]") {
                    $template['content'] = str_replace($field, (new DateTime($osnova['do']))->format("H:i"), $template['content']);
                }
                if ($field == "[cinnost" . $i . "]") {
                    $template['content'] = str_replace($field, $osnova['body'], $template['content']);
                }
                if ($field == "[hodiny" . $i . "]") {
                    $template['content'] = str_replace($field, ($timediff['decimalFormat'] * $odpracovane /60), $template['content']);
                }
            }
            if ($field == "[hodiny]") {
                $template['content'] = str_replace($field, $hoursSum/60, $template['content']);
            }*/
        }
        return $template['content'];
    }

    public function saveFileToDir($content, $filename)
    {
        $this->mpdf->WriteHTML($content);
        $this->mpdf->Output(Yii::getAlias('@webroot') . "/../docstore/skola/" . $filename, Destination::FILE);
    }


    private function differenceInHours($date, $od, $do)
    {
        $start = new DateTime($date . ' ' . $od);
        $end = new DateTime($date . ' ' . $do);
        $difference = $start->diff($end);

        return [
            'decimalFormat' => $difference->h * 60 + $difference->i,
            'stringFormat' => str_pad($difference->h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($difference->i, 2, '0', STR_PAD_LEFT)
        ];
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->student = Students::find()->where(['=', 'email', $user->email])->one();
        return $this;
    }

    public function setMonth(string $month)
    {
        $this->month = $month;
        return $this;
    }

    private function getFieldsFromTemplate(string $content): array
    {
        preg_match_all('/\[(.*?)\]/', $content, $matches);
        return $matches[0];
    }

    private function getTemplate(int $templateId): array
    {
        $template = Template::find()->where(['=', 'id', $templateId])->asArray()->one();

        return $template;
    }

    private function getMonthsFromSyllabus()
    {
        $osnovy = Osnova::find()->andWhere(['like', 'date', '-' . $this->month . '-'])->asArray()->all();
        return $osnovy;
    }

    public function setDateInput()
    {
        return $this;
    }

    public function setInputs($data)
    {
        $this->znamka = $data['znamka'];
        $this->employerName = $data['employerName'];
        $this->employerAddress = $data['employerAddress'];
        $this->datum = [
            $data['datum0'],
            $data['datum1'],
            $data['datum2'],
            $data['datum3'],
        ];
        $this->od = [
            $data['od0'],
            $data['od1'],
            $data['od2'],
            $data['od3'],
        ];
        $this->do = [
            $data['do0'],
            $data['do1'],
            $data['do2'],
            $data['do3'],
        ];
        $this->cinnost = [
            $data['cinnost0'],
            $data['cinnost1'],
            $data['cinnost2'],
            $data['cinnost3'],
        ];
        $this->hodiny = $data['hodiny'];
        $this->odpracovane = [
            $data['hodiny0'],
            $data['hodiny1'],
            $data['hodiny2'],
            $data['hodiny3'],
        ];
    }
}
