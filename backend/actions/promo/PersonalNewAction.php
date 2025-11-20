<?php
namespace backend\actions\promo;

use common\models\promo\Personal;
use Yii;
use yii\helpers\Url;

class PersonalNewAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Personal');
            $personal = new Personal();
            $personal->name_first = $data['name_first'];
            $personal->name_last = $data['name_last'];
            $personal->phone = $data['phone'];
            $personal->email = $data['email'];
            $personal->wage = $data['wage'];
            $personal->lang = implode(',',$data['lang']);
            $personal->save();
            return $this->controller->redirect(Url::to(['/promo/personal']));
        }

        return $this->controller->render('personal_new',[
            'langs' => ['hu','sk','en']
        ]);
    }

}