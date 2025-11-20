<?php

namespace backend\actions\promo;

use common\models\fbcharity\StockItemGroup;
use common\models\fbcharity\StockItemGroupLang;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class StockGroupEditAction extends Action
{
    private $langs = ['sk','hu'];
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $id = Yii::$app->request->get('id');
        $group = StockItemGroup::findOne($id);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Group');
            foreach ($this->langs as $lang) {
                $item = StockItemGroupLang::find()
                    ->where(['stock_item_group_id' => $group->id, 'lang' => $lang])
                    ->one();
                $item->title = $data[$lang]['title'];
                $item->description = $data[$lang]['description'];
                $item->save();
            }
            return $this->controller->redirect(Url::to(['/promo/stock-groups']));
        }

        return $this->controller->render('stock_group_edit', [
            'group' => $group,
            'langs' => $this->langs,
        ]);
    }
}
