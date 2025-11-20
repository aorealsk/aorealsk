<?php
namespace common\models\promo;

class PromoGuestOrderItem extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_guest_order_item';
    }

    public function getStockItemDetail()
    {
        return $this->hasOne(PromoStockItem::class,['id'=>'promo_stock_item_id']);
    }
}