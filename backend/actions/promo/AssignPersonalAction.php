<?php

namespace backend\actions\promo;

use backend\helpers\GeneratorHelper;
use common\models\fbcharity\PromoPlace;
use common\models\fbcharity\PromoPersonal;
use Yii;
use yii\helpers\Url;

class AssignPersonalAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $promoId = (int)Yii::$app->request->get('promo_id');

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $tr = Yii::$app->db->beginTransaction();
            try {
                $person = new PromoPersonal();
                $person->promo_id = $promoId;
                $person->user_name = $data['user_name'];
                $person->name_first = $data['name_first'];
                $person->name_last = $data['name_last'];
                $person->pin = $data['pin'];
                $person->email = $data['email'];
                $person->phone = $data['phone'];
                $person->wage = $data['wage'];
                $person->note = $data['note'];
                $person->created_at = date('Y-m-d H:i:s');
                $person->lang = implode(',', $data['lang']);
                $person->work_position = $data['work_position'];
                $person->place_id = $data['place_id'];
                $person->save();
                $tr->commit();
                return $this->controller->redirect(Url::to(['/promo/detail?id=' . $promoId]));
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                $tr->rollBack();
                exit;
            }
        }

        return $this->controller->render('assign_personal', [
            'promoId' => $promoId,
            'places' => PromoPlace::find()
                ->where(['=','promo_id',$promoId])
                ->andWhere(['>=','finish_date',date('Y-m-d H:i:s')])
                ->all(),
        ]);
    }

    private function checkPIN(int $promoId, int $length = 4)
    {
        $pin = GeneratorHelper::generatePIN();
        $pinOk = false;

        do {
            $pinTest = PromoPersonal::findOne(['pin' => $pin]);
            if ($pinTest) {
                $pin = GeneratorHelper::generatePIN();
            } else {
                $pinOk = true;
            }
        } while (!$pinOk);

        return $pin;
    }
}
