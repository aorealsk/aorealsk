<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class PromoOrderItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_order_items';
    }
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getDetail()
    {
        return $this->hasOne(PromoStock::class, ['id' => 'promo_stock_id']);
    }
}
