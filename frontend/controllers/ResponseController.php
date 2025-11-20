<?php

namespace frontend\controllers;

use common\models\Responses;
use Yii;
use yii\web\Controller;

class ResponseController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post('Resp');

            $resp = new Responses();
            $resp->language = $data['lang'];
            $resp->email = $data['email'];
            $resp->newsletter = isset($data['newsl']) && $data['newsl'] === 'on' ? 1 : 0;
            $resp->ip = Yii::$app->request->getUserIP();
            $resp->agent = Yii::$app->request->getUserAgent();
            $resp->save();

            $url = $data['lang'] === 'sk' ?
                'https://docs.google.com/forms/d/1cXZtF9xm2yuuIaynNu_CORzxhMg2MVA3L9mjGYFOyQw' :
                'https://docs.google.com/forms/d/1e7NDyDzjmBKPD4C94FnCoica2j21mZJ5c33EF7C4deA/';

            return $this->redirect($url);
        }
        return $this->render('index');
    }
}