<?php

namespace backend\actions\promo;

use common\models\fbcharity\Promo;
use common\models\fbcharity\PromoPersonal;
use common\models\fbcharity\Order;
use yii\helpers\Url;
use Yii;
use common\models\fbcharity\Guest;

class DetailAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        $id = (int)Yii::$app->request->get('id');
        $promo = Promo::findOne(['id' => $id]);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $attr = array_keys($promo->getOldAttributes());
            foreach ($data as $key => $item) {
                if (in_array($key, $attr)) {
                    $promo->$key = $item;
                }
            }
            $promo->save();
            return $this->controller->redirect(['/promo/detail?id=' . $id]);
        }

        return $this->controller->render('promo_detail', [
            'promo' => $promo,
            'personal' => PromoPersonal::find()->where(['=','promo_id',$id])->all(),
            'guests' => Guest::find()->all(),
            'orders' => Order::find()->all(),
        ]);
    }
}
