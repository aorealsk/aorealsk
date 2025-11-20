<?php

namespace frontend\actions\students;

use common\models\schools\StudentCourse;
use common\models\schools\StudentLanguage;
use common\models\schools\Students;
use common\models\schools\StudentSchoolReport;
use yii\base\Action;
use Yii;
use yii\helpers\Url;

class SaveFormAction extends Action
{
    /**
     * @return void
     */
    public function run(): void
    {
        $data =  Yii::$app->request->post('Student');
        $studentMotherLang = Yii::$app->request->post('StudentMotherLang');
        $studentCourse = Yii::$app->request->post('StudentCourse');
        $studentReportData = Yii::$app->request->post('Report');
        $otherLang = Yii::$app->request->post('OtherLang');

        $student = new Students();

        foreach ($data as $col => $val) {
            $student->$col = $val;
        }
        $student->save();

        if (isset($otherLang)) {
            $this->createOtherLanguageSkills($student->id, $otherLang);
        }

        $studentReport = new StudentSchoolReport();

        foreach ($studentReportData as $report) {
            foreach ($report as $col => $val) {
                $studentReport->studentId = $student->id;
                $studentReport->subject = $col;
                $studentReport->grade = $val['grade'];
                $studentReport->save();
            }
        }


        $this->createStudentLanguage($student->id, $studentMotherLang);
        $this->createStudentCourse($student->id, $studentCourse);

        $this->controller->redirect(Url::to(['/students/thank-you/' . $student->id]));
    }

    private function createStudentLanguage(int $studentId, array $motherLang): void
    {
        $studentLang = new StudentLanguage();
        $studentLang->studentId = $studentId;
        $studentLang->motherLanguage = $motherLang;
        $studentLang->languageId = $motherLang;
        $studentLang->save();
    }

    private function createStudentCourse(int $studentId, array $courseData): void
    {
        foreach ($courseData as $data) {
            $studentCourse = new StudentCourse();
            $studentCourse->student_id = $studentId;

            foreach ($data as $col => $val) {
                $studentCourse->$col = $val;
            }
            $studentCourse->save();
        }
    }

    private function createOtherLanguageSkills(int $studentId, array $otherLang)
    {
        foreach ($otherLang as $data) {
            $studentLang = new StudentLanguage();
            $studentLang->studentId = $studentId;

            foreach ($data as $col => $val) {
                $studentLang->$col = $val;
            }

            $studentLang->save();
        }
    }
}
