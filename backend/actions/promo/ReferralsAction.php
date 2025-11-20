<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoCode;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class ReferralsAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        $codes = PromoCode::find()->where(['=', 'code_type', PromoCode::REFERRAL])->all();

        return $this->controller->render('referrals', [
            'referrals' => $codes,
        ]);
    }
}
