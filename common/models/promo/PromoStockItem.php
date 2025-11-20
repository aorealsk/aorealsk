<?php
namespace common\models\promo;

class PromoStockItem extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_stock_item';
    }

    public function getItemDetails()
    {
        return $this->hasOne(StockItem::class, ['id'=>'stock_item_id']);
    }
}