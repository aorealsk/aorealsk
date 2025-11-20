<?php

namespace backend\actions\promo;

use common\models\fbcharity\PromoStock;
use common\models\fbcharity\StockItemGroup;
use common\models\fbcharity\StockItem;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class PriceListAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $promoId = Yii::$app->request->get('promo_id');
        $priceList = ArrayHelper::index(
            PromoStock::find()
                ->where(['=','promo_id',$promoId])
                ->asArray()
                ->all(),
            'stock_item_id'
        );

        if (Yii::$app->request->isPost) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $data = Yii::$app->request->post('PriceList');
                $promoId = (int)$data['promo_id'];
                $items = $data['items'];

                foreach ($items as $id => $item) {
                    $stock = StockItem::findOne(['id' => $id]);
                    $it = PromoStock::find()->where("promo_id={$promoId} and stock_item_id={$id}")->one();

                    if ($it && empty($item['item_id'])) {
                        $stock->updateAmount($it->amount, StockItem::INCREASE);
                        $it->delete();
                        continue;
                    } elseif (!$it && !empty($item['item_id'])) {
                        // este neexistuje zaznam v tabulke
                        $it = new PromoStock();
                        $it->stock_item_id = $id;
                        $it->promo_id = $promoId;
                        $it->amount = $item['amount'];
                        $it->price_04 = $item['price_04'];
                        $it->price_075 = $item['price_075'];
                        $it->price_1 = $item['price_1'];
                        $it->price_5 = $item['price_5'];
                        $it->price_10 = $item['price_10'];
                        $it->price_bottle = $item['price_bottle'];
                        $it->combo = $stock->combo;
                        $it->combo_items = $stock->combo_items;
                        $it->save();
                        $stock->updateAmount($item['amount'], StockItem::DECREASE);
                    } elseif ($it && !empty($item['item_id'])) {
                        // uz existuje zaznam v tabulke a treba amount zmenit
                        $oldAmount = $it->amount;
                        $it->amount = $item['amount'] ?? 0;
                        $it->price_04 = $item['price_04'] ?? 0;
                        $it->price_075 = $item['price_075'] ?? 0;
                        $it->price_1 = $item['price_1'] ?? 0;
                        $it->price_5 = $item['price_5'] ?? 0;
                        $it->price_10 = $item['price_10'] ?? 0;
                        $it->price_bottle = $item['price_bottle'] ?? 0;
                        $it->save();
                        if ($oldAmount != $item['amount']) {
                            $stock->updateAmount($oldAmount - $item['amount'], StockItem::DECREASE);
                        }
                    }
                }
                $tr->commit();
                return $this->controller->redirect(Url::to(['/promo/detail?id=' . $promoId]));
            } catch (\Exception $ex) {
                $tr->rollBack();
                echo $ex->getMessage();
                exit;
            }
        }

        return $this->controller->render('price_list', [
            'items' => StockItemGroup::find()
                ->andWhere(['is', 'deleted_at', null])
                ->all(),
            'pricelist' => $priceList,
        ]);
    }
}
