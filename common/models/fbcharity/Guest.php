<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;
use common\models\fbcharity\Product;

class Guest extends ActiveRecord
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const CANCELLED = 'cancelled';
    public static function tableName(): string
    {
        return 'guests'; // Replace 'customers' with your actual table name
    }
    public static function getDb()
    {
        return Yii::$app->db2; // Replace 'db2' with your actual DB2 connection component name
    }
    public function getFullName(): string
    {
        $name = $this->name_first . ' ' . $this->name_last;
        if ($this->lang === 'hu') {
            $name = $this->name_last . ' ' . $this->name_first;
        }
        return $name;
    }
    public static function findByPromoCode(string $promoCode)
    {
        $code = explode('-', $promoCode);
        array_pop($code);
        $promoCode = implode('-', $code);
        return static::find()->where(['like', 'promo_code', $promoCode . '%', false])->one();
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
