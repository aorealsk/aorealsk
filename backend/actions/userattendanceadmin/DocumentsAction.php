<?php
namespace backend\actions\userattendanceadmin;

use common\models\Office;
use common\models\PrivilegesTemplates;
use Yii;
use yii\helpers\Url;

class DocumentsAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        return $this->controller->render('documents',[
            'privileges' => PrivilegesTemplates::find()->where(['=', 'user_function', 0])->groupBy('group_name')->all(),
            'offices' => Office::find()->all()
        ]);
    }
}