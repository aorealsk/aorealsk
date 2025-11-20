<?php

namespace frontend\actions\questionnaire;

use common\models\Mesto;
use Yii;
use yii\base\Action;
use yii\web\Response;

class FetchTownAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Mesto::find()->where(['like', 'psc', Yii::$app->request->post('zip')])->all();
    }
}
