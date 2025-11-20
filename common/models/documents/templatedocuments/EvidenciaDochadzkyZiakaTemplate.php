<?php

namespace common\models\documents\templatedocuments;

use Yii;
use DateTime;
use common\models\User;
use common\models\Template;
use Mpdf\Output\Destination;
use common\helpers\DateHelper;
use common\models\schools\School;
use common\models\schools\Students;
use common\models\schools\StudyField;
use common\models\users\UserAttendance;
use DateTimeImmutable;

class EvidenciaDochadzkyZiakaTemplate extends PdfTemplateDocument
{
    const TEMPLATE_ID = 14;
    private $month;
    private $student;
    private $user;
    private $dateInput;
    private $instructor;

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
        $school = School::find()->where(['=', 'id', $this->student->schoolId])->one();

        $template = $this->getTemplate(self::TEMPLATE_ID);
        $fields = $this->getFieldsFromTemplate($template['content']);

        $days = DateHelper::getDays();
        $months = DateHelper::getAllMonths();

        foreach ($fields as $field) {
            if ($field == '[student_last_name]') {
                $template['content'] = str_replace($field, $this->student->lastName, $template['content']);
            } else if ($field == '[student_full_name]') {
                $template['content'] = str_replace($field, $this->student->lastName . ' ' . $this->student->firstName, $template['content']);
            } else if ($field == '[school]') {
                $template['content'] = str_replace($field, $school->description . ' ' . $school->address . ' ' . $school->town . ' ' . $school->zip, $template['content']);
            } else if ($field == '[student_birth_date]') {
                $template['content'] = str_replace($field, $this->student->birthDate ?? '-', $template['content']);
            } else if ($field == '[student_address]') {
                $template['content'] = str_replace($field, $this->student->fullAddress . ' ' . $this->student->zip . ' ' . $this->student->town, $template['content']);
            } else if ($field == '[study_field]') {
                $template['content'] = str_replace($field, $this->student->fullAddress . ' ' . $this->getStudyField() . ' ' . $this->student->town, $template['content']);
            } elseif ($field == '[input.date]') {
                $template['content'] = str_replace($field, (new DateTimeImmutable($this->dateInput))->format('d.m.Y'), $template['content']);
            } elseif ($this->instructor != null && $field == '[instructor]') {
                $template['content'] = str_replace($field, $this->instructor->username, $template['content']);
            } elseif ($field == '[student_class]') {
                $template['content'] = str_replace($field, $this->student->class, $template['content']);
            }
            foreach ($months as $month) {
                foreach ($days as $day) {
                    if ($field == '[' . $month . '-' . $day . ']') {
                        $template['content'] = str_replace($field, $this->getUserAttendance($month, $day), $template['content']);
                    }
                }
            }
        }

        return $template['content'];
    }

    public function saveFileToDir($content, $filename)
    {
        $this->mpdf->WriteHTML($content);

        $this->mpdf->Output(Yii::getAlias('@webroot') . "/../docstore/skola/" . $filename, Destination::FILE);
    }

    private function getUserAttendance($month, $day)
    {
        $att =  UserAttendance::find()->where(['=', 'userId', $this->user->id])->andWhere(['like', 'uaDate', '-' . $month . '-' . $day])->one();

        if ($att) {
            $diff = $this->differenceInHours($att->uaDate, $att->inTime, $att->outTime);
        }

        return $diff ?? '';
    }

    private function differenceInHours($date, $od, $do)
    {
        $start = new DateTime($date . ' ' . $od);
        $end = new DateTime($date . ' ' . $do);
        $difference = $start->diff($end);

        return round(($difference->h * 60 + $difference->i) / 60, 1);
    }

    private function getFieldsFromTemplate($content)
    {
        preg_match_all('/\[(.*?)\]/', $content, $matches);
        return $matches[0];
    }

    private function getTemplate($id)
    {
        return Template::find()->where(['=', 'id', $id])->asArray()->one();
    }

    private function getStudyField()
    {
        $field = StudyField::find()->where(['=', 'id', $this->student->studyFieldId])->one();

        return $field->name;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

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

    public function setTeacherSignature()
    {
        return $this;
    }

    public function setZastupcaSignature()
    {
        return $this;
    }

    public function setInputs($data)
    {
        // instructor
        $this->instructor = User::findOne($data['instructor']);
    }
}
