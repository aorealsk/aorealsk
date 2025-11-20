<?php

namespace common\models\documents\templatedocuments;

use common\helpers\DateHelper;
use common\models\users\UserAttendance;
use common\models\users\UserAttendanceType;
use Yii;
use DateTimeImmutable;
use common\models\User;
use common\models\Template;
use Mpdf\Output\Destination;
use common\models\Agent;

class HlasenieOAbsenciiZiakaTemplate extends PdfTemplateDocument
{
    public const TEMPLATE_ID = 13;

    private $student;
    private $month;
    private $dateInput;
    private $instructor;
    private $users = null;
    private $employerName;
    private $employerAddress;

    public function create(): void
    {
    }

    public function writeToFile(): void
    {
    }

    public function getFileName(): string
    {
        $template = Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one();
        return iconv('UTF-8', 'ASCII//TRANSLIT', str_replace([" ","/"],"_",$template->name."_".DateHelper::getMonthText($this->month).".pdf"));
    }

    public function getExportName(): string
    {
        $template = Template::find()->where(['=', 'id', self::TEMPLATE_ID])->one();

        return $template->export_name;
    }

    public function process()
    {
        $template = $this->getTemplate(self::TEMPLATE_ID);

        $fields = $this->getFieldsFromTemplate($template['content']);

        foreach ($fields as $field) {
            if ($field === '[input.month]') {
                $template['content'] = str_replace($field, DateHelper::getMonthText($this->month), $template['content']);
            }
            if ($field === '[employer.name]') {
                $template['content'] = str_replace($field, $this->employerName, $template['content']);
            }
            if ($field === '[input.date]') {
                $template['content'] = str_replace($field, (new DateTimeImmutable($this->dateInput))->format('d.m.Y'), $template['content']);
            }
            if ($this->instructor != null && $field == '[instructor]') {
                $template['content'] = str_replace($field, $this->instructor->username, $template['content']);
            }
            if ($field === '[employer.full_address]') {
                $template['content'] = str_replace($field, $this->employerAddress, $template['content']);
            }
            if ($this->instructor && $field === '[instructor.full_name]') {
                $template['content'] = str_replace($field, $this->instructor, $template['content']);
            }
        }

        $userList = explode(',',$this->users);
        $dochadzka = new UserAttendance();
        foreach($userList as $id => $item) {
            $user = Agent::findOne(['user_id'=>$item]);
            $template['content'] = str_replace("[order_number_" . ($id+1) ."]", str_pad(($id+1),2,'0', STR_PAD_LEFT).'.', $template['content']);
            $template['content'] = str_replace("[trainee_full_name_" . ($id+1) ."]", $user->getFullName(), $template['content']);
            // vypocet dochadzky
            $ospravedlnene = $dochadzka->getMonthlyHoursByTypeByUser($item, UserAttendance::ABSENCE, $this->month);
            $ospravedlnene += $dochadzka->getMonthlyHoursByTypeByUser($item, UserAttendance::DOCTOR_VISIT, $this->month);
            $ospravedlnene += $dochadzka->getMonthlyHoursByTypeByUser($item, UserAttendanceType::SICKNESS_ABSENCE, $this->month);
            $neospravedlnene = $dochadzka->getMonthlyHoursByTypeByUser($item, UserAttendance::UNVERIFIED_ABSENCE, $this->month);
            $nedoriesene = $dochadzka->getMonthlyHoursByTypeByUser($item, UserAttendance::UNSOLVED_ABSENCE, $this->month);
            $template['content'] = str_replace("[dochadzka_ospravedlnena_" . ($id+1) ."]", round(($ospravedlnene ?? 0)/3600,2), $template['content']);
            $template['content'] = str_replace("[dochadzka_neospravedlnena_" . ($id+1) ."]", round(($neospravedlnene ?? 0)/3600,2), $template['content']);
            $template['content'] = str_replace("[dochadzka_nedoriesena_" . ($id+1) ."]", round(($nedoriesene ?? 0)/3600,2), $template['content']);
            $template['content'] = str_replace("[dochadzka_spolu_" . ($id+1) ."]", round(($ospravedlnene + $nedoriesene + $neospravedlnene)/3600,2), $template['content']);
        }

        $template['content'] = $this->fillEmptyData($userList, $template['content']);

        return $template['content'];
    }

    private function fillEmptyData(array $userList, string $content): string
    {
        $userCount = count($userList);
        for($i=$userCount+1; $i < 4; $i++ ) {
            $content = str_replace("[order_number_{$i}]", '&nbsp;', $content);
            $content = str_replace("[trainee_full_name_{$i}]", '', $content);
            $content = str_replace("[dochadzka_ospravedlnena_{$i}]", '', $content);
            $content = str_replace("[dochadzka_neospravedlnena_{$i}]", '',$content);
            $content = str_replace("[dochadzka_nedoriesena_{$i}]", '', $content);
            $content = str_replace("[dochadzka_spolu_{$i}]", '', $content);
        }

        return $content;
    }

    public function saveFileToDir($content, $filename)
    {
        $this->mpdf->WriteHTML($content);
        $this->mpdf->Output(Yii::getAlias('@webroot') . "/../docstore/skola/" . $filename, Destination::FILE);
    }


    private function getFieldsFromTemplate($content)
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
        //$this->student = Students::find()->where(['=', 'email', $user->email])->one();
        $this->users = $user->id;
        return $this;
    }

    public function setUsers($userList)
    {
        $this->users = $userList;
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

    public function setTeacherSignature(string $teacher)
    {
        return $this;
    }

    public function setZastupcaSignature()
    {
        return $this;
    }

    public function setInputs($data)
    {
        $this->instructor = $data['instructor'];
        $this->employerName = $data['employerName'];
        $this->employerAddress = $data['employerAddress'];
    }
}
