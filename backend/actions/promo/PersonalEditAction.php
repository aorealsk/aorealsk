<?php

namespace backend\actions\promo;

use Yii;
use common\models\promo\Personal;
use yii\helpers\Url;

class PersonalEditAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $id = (int)Yii::$app->request->get('id');
        $personal = Personal::findOne(['id'=>$id]);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Personal');
            $personal->name_first = $data['name_first'];
            $personal->name_last = $data['name_last'];
            $personal->phone = $data['phone'];
            $personal->email = $data['email'];
            $personal->wage = $data['wage'];
            $personal->lang = implode(',',$data['lang']);
            $personal->save();
            $this->controller->redirect(Url::to(['/promo/personal']));
        }


        return $this->controller->render('personal_edit',[
            'langs' => ['hu','sk','en'],
            'personal' => $personal
        ]);
    }
}