<?php

namespace backend\actions\promo;

use yii\base\Action;
use yii\helpers\Url;
use Yii;
use common\models\fbcharity\PromoCode;

class CodesAddAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('PromoCode');
            $code = new PromoCode();

        }

        return $this->controller->render('codes_add');
    }
}
