<?php
namespace backend\actions\templatevars;

use common\models\TemplateVarsCols;
use common\models\TemplateVarsMap;
use common\models\TemplateVarsRows;
use Yii;
use yii\base\Action;
use yii\helpers\Url;

class IndexAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('index',[
            'cols' => TemplateVarsCols::find()->andWhere(['=','status',1])->asArray()->all(),
            'rows' => TemplateVarsRows::find()->andWhere(['=','status',1])->asArray()->all(),
            'fullmap' => (new TemplateVarsMap())->getFullMap(),
        ]);
    }
}