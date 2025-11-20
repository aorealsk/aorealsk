<?php

namespace backend\actions\promo;

use yii\base\Action;
use yii\helpers\Url;
use Yii;
use common\models\fbcharity\PromoCode;

class EditReferralAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $id = Yii::$app->request->get('id');
        $code = PromoCode::findOne($id);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $code->code = $data['code'];
            $code->available_from = $data['available_from'];
            $code->available_to = $data['available_to'];
            $code->assigned_to = $data['assigned_to'];
            $code->save();

            return $this->controller->redirect(Url::to(['/promo/referrals']));
        }

        return $this->controller->render('edit-referral', [
            'code' => $code
        ]);
    }
}
