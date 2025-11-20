<?php

namespace backend\controllers;

use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class ExternalOffersController extends Controller
{
    public function actionIndex()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }
        return $this->render('index');
    }
}
