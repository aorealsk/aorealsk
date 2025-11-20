<?php

namespace backend\actions\promo;

use common\models\fbcharity\Guest;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class GuestDetailAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $promoId = Yii::$app->request->get('pid');
        $gid = Yii::$app->request->get('gid');
        $guest = Guest::findOne($gid);

        return $this->controller->render('guest-detail', [
            'guest' => $guest,
            'promoId' => $promoId,
        ]);
    }
}
