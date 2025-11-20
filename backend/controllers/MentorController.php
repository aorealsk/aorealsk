<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\models\MentorProfile;
use common\models\Team;
use common\models\schools\Students;

class MentorController extends Controller
{
    public function behaviors()
    {
        return [
            // Only authenticated users can access these pages (simple AccessControl / ACF)
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['profile','teams','team-create','team-update'],
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'team-create' => ['post','get'],
                    'team-update' => ['post','get'],
                ],
            ],
        ];
    }

    /** Create or update mentor profile for current user. */
    public function actionProfile()
    {
        $model = MentorProfile::findOne(['user_id' => Yii::$app->user->id])
              ?? new MentorProfile(['user_id'=>Yii::$app->user->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Profil uložený.');
            return $this->redirect(['teams']);
        }

        return $this->render('profile', ['model' => $model]);
    }

    public function actionDownloadMentorDoc()
    {
    $path = Yii::getAlias('@backend/views/mentor/documents/for_teachers_and_supervisors.xlsx');
    if (file_exists($path)) {
        return Yii::$app->response->sendFile($path, 'for_teachers_and_supervisors.xlsx');
    }
    throw new \yii\web\NotFoundHttpException('File not found.');
    }

    public function actionDownloadPartners()
    {
    $path = Yii::getAlias('@backend/views/partners/documents/for_partners.xlsx');
    if (!file_exists($path)) {
        throw new NotFoundHttpException('A kért fájl nem található.');
    }
    return Yii::$app->response->sendFile($path, 'for_partners.xlsx');
    }


    /** List teams for current mentor. */
    public function actionTeams()
    {
        $profile = MentorProfile::findOne(['user_id' => Yii::$app->user->id]);
        if (!$profile) return $this->redirect(['profile']);

        $teams = Team::find()
            ->where(['mentor_profile_id' => $profile->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('teams/index', ['profile' => $profile, 'teams' => $teams]);
    }

    /** Create a new team and pick members. */
    public function actionTeamCreate()
    {
        $profile = MentorProfile::findOne(['user_id' => Yii::$app->user->id]);
        if (!$profile) return $this->redirect(['profile']);

        $model = new Team(['mentor_profile_id' => $profile->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->saveStudents((array)$model->studentIds);
            Yii::$app->session->setFlash('success', 'Tím vytvorený.');
            return $this->redirect(['teams']);
        }

        // Sort by real DB columns, then format label in PHP
        $students = Students::find()
            ->orderBy(['LastName' => SORT_ASC, 'FirstName' => SORT_ASC])
            ->all();

        $studentMap = ArrayHelper::map($students, 'id', function ($m) {
            // If your AR exposes $m->studentName (computed), prefer it; otherwise build from DB fields
            if (isset($m->studentName) && $m->studentName) {
                return $m->studentName;
            }
            $first = $m->FirstName ?? '';
            $last  = $m->LastName  ?? '';
            $full  = trim($first . ' ' . $last);
            return $full !== '' ? $full : ('ID ' . $m->id);
        });

        return $this->render('teams/form', [
            'model'      => $model,
            'studentMap' => $studentMap,
            'isUpdate'   => false,
        ]);
    }

    /** Update existing team + members. */
    public function actionTeamUpdate($id)
    {
        $profile = MentorProfile::findOne(['user_id' => Yii::$app->user->id]);
        if (!$profile) return $this->redirect(['profile']);

        $model = Team::findOne(['id' => $id, 'mentor_profile_id' => $profile->id]);
        if (!$model) throw new NotFoundHttpException('Team not found.');

        // preload selected students
        $model->studentIds = ArrayHelper::getColumn($model->students, 'id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->saveStudents((array)$model->studentIds);
            Yii::$app->session->setFlash('success', 'Tím uložený.');
            return $this->redirect(['teams']);
        }

        $students = Students::find()
            ->orderBy(['LastName' => SORT_ASC, 'FirstName' => SORT_ASC])
            ->all();

        $studentMap = ArrayHelper::map($students, 'id', function ($m) {
            if (isset($m->studentName) && $m->studentName) {
                return $m->studentName;
            }
            $first = $m->FirstName ?? '';
            $last  = $m->LastName  ?? '';
            $full  = trim($first . ' ' . $last);
            return $full !== '' ? $full : ('ID ' . $m->id);
        });

        return $this->render('teams/form', [
            'model'      => $model,
            'studentMap' => $studentMap,
            'isUpdate'   => true,
        ]);
    }
}
