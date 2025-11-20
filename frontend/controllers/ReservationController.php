<?php

namespace frontend\controllers;

use common\models\Stat;
use Yii;
use yii\base\InvalidConfigException;
use common\helpers\LanguageHelper;
use yii\web\Controller;

class ReservationController extends Controller
{
    private array $allowedLanguages = ['hu', 'sk'];
    private string $defaultLanguage = 'sk';
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $lang = $this->getLanguage(Yii::$app->request->headers->get('accept-language'));
        $actionID = Yii::$app->request->get('action');
        if (!empty($actionID)) {
            return $this->createAction($actionID)->runWithParams([]);
        }

        return $this->render('index', [
            'staty' => Stat::find()->where(['=', 'status', 1])->all(),
        ]);
    }
    protected function getLanguage(string $browserLanguage): string
    {
        $lang = LanguageHelper::calculatei18nCode($browserLanguage);
        if (in_array($lang, $this->allowedLanguages)) {
            return $lang;
        }
        return $this->defaultLanguage;
    }
}
