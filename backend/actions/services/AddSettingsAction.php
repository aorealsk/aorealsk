<?php

namespace backend\actions\services;

use common\models\Sluzby;
use common\models\sys\SysLog;
use Yii;
use yii\helpers\Url;

class AddSettingsAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Service');

            $tr = Yii::$app->db->beginTransaction();
            try {
                $service = new Sluzby();
                foreach ($data as $key => $value) {
                    $service->$key = $value;
                }
                $service->save();
                $tr->commit();
                return $this->controller->redirect(Url::to(['services/settings']));
            } catch (Exception $e) {
                SysLog::WriteError(null, __CLASS__, $e->getTraceAsString(), __LINE__);
                Yii::$app->session->setFlash('error', $e->getTraceAsString());
                $tr->rollBack();
            }
        }
        return $this->controller->render('settings/add');
    }
}
