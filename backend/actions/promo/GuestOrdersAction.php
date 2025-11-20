<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoOrder;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class GuestOrdersAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $pid = Yii::$app->request->get('pid');
        $orders = PromoOrder::find()->where(['=', 'promo_id', $pid])->all();

        return $this->controller->render('orders', [
            'orders' => $orders,
            'promoId' => $pid,
        ]);
    }
}
