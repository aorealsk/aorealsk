<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoPlace;
use Yii;
use yii\base\Action;
use yii\helpers\Url;

class PlacesAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('promo_places', [
            'places' => PromoPlace::find()->where(['is', 'deleted_at', null])->all(),
        ]);
    }
}
