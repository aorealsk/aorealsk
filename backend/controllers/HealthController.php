<?php
namespace backend\controllers;

use yii\web\Controller;

class HealthController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    public function actionVer()
    {
        phpinfo();
    }
}