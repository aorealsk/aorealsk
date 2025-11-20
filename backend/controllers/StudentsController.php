<?php

namespace backend\controllers;

use common\helpers\StringHelper;
use common\models\Agent;
use common\models\schools\School;
use common\models\schools\Students;
use common\models\schools\StudyField;
use common\models\User;
use common\models\MentorProfile;   // ← added
use Yii;
use yii\web\Controller;
use yii\web\Response;

class StudentsController extends Controller
{
    /**
     * Preload mentors (teachers / supervisors / business partners) for the index page.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if ($action->id === 'index') {
            $roles = ['teacher', 'supervisor', 'business_partner'];
            $mentorsByRole = [];

            foreach ($roles as $role) {
                $mentorsByRole[$role] = MentorProfile::find()
                    ->where(['role' => $role])
                    ->with('user') // eager-load related User for display name/email
                    ->orderBy(['org_name' => SORT_ASC, 'id' => SORT_ASC])
                    ->all();
            }

            Yii::$app->view->params['mentorsByRole'] = $mentorsByRole;
        }

        return true;
    }

    // Schedule Download:
    public function actionDownloadSchedule()
    {
    $path = Yii::getAlias('@backend/views/students/documents/schedule_students.xlsx');
    if (file_exists($path)) {
        return Yii::$app->response->sendFile($path, 'schedule_students.xlsx');
    }
    throw new \yii\web\NotFoundHttpException('File not found.');
    }



    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'index' => [
                'class' => 'backend\actions\students\IndexAction'
            ],
            'reports' => [
                'class' => 'backend\actions\students\ReportsAction'
            ]
        ];
    }

    public function actionUpdateIban()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Itt kellene lennie az IBAN mentési logikának
        return ['status' => 'ok'];
    }

    public function actionMakeUser()
    {
        // Ez a funkció megmarad, ahogy volt
        Yii::$app->response->format = Response::FORMAT_JSON;
        $uid = Yii::$app->request->post('uid');
        $student = Students::findOne($uid);

        $username = $this->makeUsername($student->firstName, $student->lastName);

        $user = new User();
        $user->username = $username;
        $user->email = $student->email;
        $user->auth_key = Yii::$app->security->generateRandomString();
        $password = '+' . ucfirst($student->firstName) . ucfirst($student->lastName) . date('Y') . '+';
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);
        $user->save();

        // ... a többi része a funkciónak változatlan ...

        return $this->redirect('/backoffice/students');
    }

    /**
     * Ez a metódus felel a diák részletes adatainak AJAX-os lekérdezéséért.
     * @param int $id A diák azonosítója
     * @return string A renderelt HTML kód
     */
    public function actionGetDetails($id)
    {
        $student = Students::find()
            ->where(['id' => $id])
            ->with([
                'studentLegalRepresentatives',
                'studentLanguages.jazyk', // Feltételezi, hogy a StudentLanguage modellben van 'jazyk' reláció
            ])
            ->one();

        if ($student) {
            return $this->renderPartial('_studentDetails', [
                'student' => $student,
            ]);
        }

        return '<p class="text-danger">A diák nem található.</p>';
    }

    private function makeUsername(string $firstName, string $lastName): string
    {
        return strtolower(StringHelper::replaceAccents($firstName)) .  strtolower(StringHelper::replaceAccents($lastName));
    }
}
