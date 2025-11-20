<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoCode;
use Yii;
use yii\base\Action;
use yii\helpers\Url;

class CodesAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $codes = PromoCode::find()->where(['!=', 'code_type', PromoCode::REFERRAL])->all();

        return $this->controller->render('codes', [
            'codes' => $codes,
        ]);
    }
}
