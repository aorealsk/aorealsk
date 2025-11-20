<?php
namespace backend\actions\templatevars;

use common\models\TemplateVars;
use Yii;
use yii\helpers\Url;

class ManagerAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('manager',[
            'rows' => TemplateVars::find()->orderBy('id desc')->all()
        ]);
    }
}