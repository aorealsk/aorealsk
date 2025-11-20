<?php

namespace backend\actions\promo;

use yii\base\Action;
use Yii;
use yii\helpers\Url;

class PromoClosureAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $promoId = Yii::$app->request->get('promo_id');

        return $this->controller->render('promo-closure', [
            'promoId' => $promoId,
        ]);
    }
}
