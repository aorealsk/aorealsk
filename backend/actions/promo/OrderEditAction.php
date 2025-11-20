<?php

namespace backend\actions\promo;

use yii\base\Action;
use yii\helpers\Url;
use Yii;
use common\models\fbcharity\Order;

class OrderEditAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        $oid = Yii::$app->request->get('oid');
        $order = Order::findOne(['id' => $oid]);

        return $this->controller->render('order_edit', [
            'order' => $order,
        ]);
    }
}
