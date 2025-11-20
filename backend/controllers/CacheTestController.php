<?php

namespace backend\controllers;

use yii\web\Controller;
use Yii;

class CacheTestController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->cache->set('elso', 1);
        echo Yii::$app->cache->get('elso');
    }
}