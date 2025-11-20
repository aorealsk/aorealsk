<?php

namespace backend\actions\promo;

use common\models\fbcharity\StockItemGroup;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class StockGroupsAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $groups = StockItemGroup::find()->all();

        return $this->controller->render('stock_groups', [
            'groups' => $groups
        ]);
    }
}
