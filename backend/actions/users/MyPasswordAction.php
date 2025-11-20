<?php
namespace backend\actions\users;

use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use common\models\User;
use backend\models\users\ChangePasswordForm;

class MyPasswordAction extends Action
{
    // URL: /backoffice/users/my-password  (nincs id)
    public function run()
    {
        $uid = Yii::$app->user->id;
        $user = $uid ? User::findOne($uid) : null;
        if (!$user) throw new NotFoundHttpException('Felhasználó nem található.');

        $model = new ChangePasswordForm(['user'=>$user]);
        $model->setScenario(ChangePasswordForm::SCENARIO_SELF);

        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Yii::$app->session->setFlash('success','A jelszavad frissítve.');
            return $this->controller->redirect(['/site/index']);
        }

        return $this->controller->render('change-password',[
            'model'=>$model,
            'user'=>$user,
        ]);
    }
}
