<?php

namespace frontend\controllers;

use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class TiperController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        \Yii::$app->assetManager->bundles = false;
        $this->layout = 'tiper';
        return $this->render('index', [
            'user' => $_GET['u'] ?? null,
            'source' => $_GET['s'] ?? null,
        ]);
    }
}
