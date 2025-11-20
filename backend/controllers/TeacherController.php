<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\models\TeacherSearch;
use common\models\partners\Partners;
use common\models\partners\PartnerType;

class TeacherController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']], // csak bejelentkezett admin/backoffice
                ],
            ],
        ];
    }

    public function actionDownloadMentorDoc()
    {
    $path = Yii::getAlias('@backend/views/mentor/documents/for_teachers_and_supervisors.xlsx');
    if (file_exists($path)) {
        return Yii::$app->response->sendFile($path, 'for_teachers_and_supervisors.xlsx');
    }
    throw new \yii\web\NotFoundHttpException('File not found.');
    }

    public function actionIndex()
    {
        $searchModel = new TeacherSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // iskolák a filterhez
        $schools = Partners::find()
            ->select(['id','partner_name'])
            ->where(['status'=>1, 'partner_type'=>PartnerType::SCHOOL])
            ->orderBy('partner_name')
            ->asArray()->all();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'schools'      => $schools,
        ]);
    }
}
