<?php
namespace common\models\promo;

class PromoGuedtOrderItem extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_guest_order_item';
    }
}