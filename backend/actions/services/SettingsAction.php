<?php

namespace backend\actions\services;

use Yii;
use yii\helpers\Url;
use common\models\Sluzby;

class SettingsAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('settings/index', [
            'services' => Sluzby::find()->orderBy('id desc')->all()
        ]);
    }
}