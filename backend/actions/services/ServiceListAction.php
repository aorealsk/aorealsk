<?php

namespace backend\actions\services;

use Yii;
use yii\helpers\Url;
use common\models\Sluzby;
use yii\base\Action;

class ServiceListAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('services/index', [
            'services' => Sluzby::find()->orderBy('id desc')->all()
        ]);
    }
}