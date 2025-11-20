<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class QuestionnaireController extends Controller
{

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (is_null(Yii::$app->user->identity)) {
            $this->redirect(Url::to(['/client/login']));
            return false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'index' => [
                'class' =>  'frontend\actions\questionnaire\IndexAction'
            ],
            'form-submit' => [
                'class' => 'frontend\actions\questionnaire\FormSubmitAction'
            ],
            'fetch-town' => [
                'class' => 'frontend\actions\questionnaire\FetchTownAction'
            ],
            'convert-num-to-text' => [
                'class' => 'frontend\actions\questionnaire\ConvertNumToWordAction'
            ],
            'submit-doc' => [
                'class' => 'frontend\actions\questionnaire\SubmitDocAction'
            ],
            'thank-you' => [
                'class' => 'frontend\actions\questionnaire\ThankYouAction'
            ]
        ];
    }
}
