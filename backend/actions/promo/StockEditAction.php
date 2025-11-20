<?php

namespace backend\actions\promo;

use common\models\fbcharity\StockItem;
use common\models\fbcharity\StockItemGroupLang;
use common\models\fbcharity\StockItemLang;
use common\models\fbcharity\StockItemMedia;
use yii\helpers\Url;
use Yii;

class StockEditAction extends \yii\base\Action
{
    private $itemFields = [
        'alcohol', 'cost', 'bottle_size',
        'carton', 'bottle_per_carton', 'amount', 'bottle_cnt',
        'investment','price_1','price_1_bottle','price_04', 'price_04_bottle',
        'price_075', 'price_075_bottle', 'price_5', 'price_10', 'price_bottle',
    ];
    private $langs = ['sk','hu'];
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        $id = (int)Yii::$app->request->get('id');
        $item = StockItem::findOne(['id' => $id]);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Item');
            $tr = Yii::$app->db->beginTransaction();
            try {
                foreach ($this->itemFields as $field) {
                    $item->$field = $data[$field];
                }
                $item->save();
                foreach ($this->langs as $lang) {
                    $itemLang = StockItemLang::find()->where("lang='{$lang}' and stock_item_id={$id}")->one();
                    $itemLang->title = $data[$lang]['title'];
                    $itemLang->description = $data[$lang]['description'];
                    $itemLang->save();
                }
                $tr->commit();

                // storing media file
                $files = $_FILES['pics'] ?? null;

                if (!empty($files['name'])) {
                    $destDir = Yii::getAlias('@webroot') . '/../../media/stock/';
                    if (move_uploaded_file($files["tmp_name"], $destDir . $files["name"])) {
                        $media = new StockItemMedia();
                        $media->stock_item_id = $item->id;
                        $media->file_type = $files['type'];
                        $media->file_name = $files['name'];
                        $media->created_at = date('Y-m-d H:i:s');
                        $media->save();
                    } else {
                        throw new \Exception('File upload failed');
                    }
                }

                return $this->controller->redirect(Url::to(['/promo/stock']));
            } catch (\Exception $e) {
                $tr->rollBack();
                echo $e->getMessage();
                exit;
            }
        }

        return $this->controller->render('stock_edit', [
            'stockitem' => $item,
            'langs' => $this->langs,
            'groups' => StockItemGroupLang::find()
                ->select(['stock_item_group_id', 'title'])
                ->andWhere(['=', 'lang', 'sk'])
                ->andWhere(['is', 'deleted_at', null])
                ->asArray()
                ->all(),
        ]);
    }
}