<?php

namespace backend\actions\promo;

use common\models\fbcharity\Promo;
use common\models\fbcharity\PromoPlace;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class AddPromoPlaceAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('PromoPlace');
            $place = new PromoPlace();
            foreach ($data as $key => $value) {
                $place->$key = $value;
            }
            $place->created_at = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $place->save();
            return $this->controller->redirect(Url::to(['/promo/places']));
        }

        return $this->controller->render('add_promo_place', [
            'promotions' => Promo::find()->where(['is', 'deleted_at', null])->all(),
        ]);
    }
}
