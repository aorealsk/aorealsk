<?php

namespace frontend\actions\questionnaire;

use backend\helpers\HelperString;
use Yii;
use yii\base\Action;
use yii\web\Response;

class ConvertNumToWordAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        return HelperString::number2words(Yii::$app->request->post('num'));
    }
}
