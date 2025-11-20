<?php

namespace backend\actions\services;

use common\models\sys\SysLog;
use Yii;
use common\models\Sluzby;
use yii\helpers\Url;

class EditSettingsAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        $serviceId = Yii::$app->request->get('id');

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Service');
            $service = Sluzby::findOne(['id' => $serviceId]);
            $tr = Yii::$app->db->beginTransaction();
            try {
                foreach ($data as $key => $value) {
                    $service->$key = $value;
                }
                $service->save();
                $tr->commit();
                return $this->controller->redirect(Url::to(['services/settings']));
            } catch (\Exception $e) {
                SysLog::WriteError(null, __CLASS__, $e->getTraceAsString(), __LINE__);
                Yii::$app->session->setFlash('error', $e->getTraceAsString());
                $tr->rollBack();
            }
        }

        return $this->controller->render('settings/edit', [
            'service' => Sluzby::findOne(['id' => $serviceId])
        ]);
    }
}