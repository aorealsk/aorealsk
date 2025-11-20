<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoPlace;
use yii\base\Action;
use yii\helpers\Url;
use Yii;
use common\models\fbcharity\PromoPersonal;

class EditPersonalAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $personalId = Yii::$app->request->get('pid');
        $promoId = Yii::$app->request->get('pro');
        $personal = PromoPersonal::findOne(['id' => $personalId]);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $personal->user_name = $data['user_name'];
            $personal->pin = $data['pin'];
            $personal->name_first = $data['name_first'];
            $personal->name_last = $data['name_last'];
            $personal->phone = $data['phone'];
            $personal->email = $data['email'];
            $personal->wage = $data['wage'];
            $personal->lang = implode(',', $data['lang']);
            $personal->place_id = $data['place_id'];
            $personal->work_position = $data['work_position'];
            $personal->note = $data['note'];
            $personal->save();

            return $this->controller->redirect(Url::to(['/promo/detail', 'id' => $promoId]));
        }

        return $this->controller->render('edit-personal', [
            'promoId' => $promoId,
            'personal' => $personal,
            'places' => PromoPlace::find()
                ->where(['=','promo_id',$promoId])
                ->andWhere(['>=','finish_date',date('Y-m-d H:i:s')])
                ->all(),
        ]);
    }
}
