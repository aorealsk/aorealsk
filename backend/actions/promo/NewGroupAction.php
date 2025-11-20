<?php
namespace backend\actions\promo;

//use common\models\promo\StockItemGroup;
use common\models\promo\StockItemGroup;
use common\models\promo\StockItemGroupLang;
use Yii;
use yii\helpers\Url;

class NewGroupAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Group');
            $tr = Yii::$app->db->beginTransaction();
            try {
                if (!$this->validateGroups($data)) {
                    throw new \Exception('Inserted data are invalid!');
                }
                $group = new StockItemGroup();
                $group->save();

                foreach ($data as $lang => $item) {
                    $groupLang = new StockItemGroupLang();
                    $groupLang->stock_item_group_id = $group->id;
                    $groupLang->title = $item['title'];
                    $groupLang->description = $item['description'] ?? '';
                    $groupLang->lang = $lang;
                    $groupLang->save();
                }
                $tr->commit();
                return $this->controller->redirect(Url::to(['/promo/stock']));
            } catch (\Exception $ex) {
                $tr->rollBack();
            }
        }

        return $this->controller->render('new_group', [
            'langs' => ['sk','hu'],
        ]);
    }

    private function validateGroups(array $data): bool
    {
        $valid = false;
        foreach ($data as $item) {
            $valid = !empty($item);
        }
        return $valid;
    }
}