<?php

namespace backend\actions\students;

use common\models\Office;
use common\models\schools\Students;
use Yii;
use yii\base\Action;
use yii\helpers\Url;

class IndexAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        // Az új, hatékony adatlekérdezés ActiveRecord-del
        $students = Students::find()
            ->with([
                'school',
                'studyField',
            ])
            ->andWhere(['!=', 'firstName', '']) // Keresztnév nem lehet üres string
            ->andWhere(['IS NOT', 'firstName', null]) // Keresztnév nem lehet NULL
            ->andWhere(['!=', 'lastName', ''])  // Vezetéknév nem lehet üres string
            ->andWhere(['IS NOT', 'lastName', null])   // Vezetéknév nem lehet NULL
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->controller->render('index', [
            'students' => $students,
            'offices' => Office::find()->all()
        ]);
    }
}