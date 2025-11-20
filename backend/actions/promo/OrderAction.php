<?php

namespace backend\actions\promo;

use yii\helpers\Url;
use Yii;

class OrderAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        return $this->controller->render('order');
    }
}