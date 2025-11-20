<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoPersonal;
use yii\helpers\Url;
use Yii;
use yii\base\Action;

class PersonalAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        return $this->controller->render('personal', [
            'personal' => PromoPersonal::find()->all()
        ]);
    }
}
