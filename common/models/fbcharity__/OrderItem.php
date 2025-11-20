<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class OrderItem extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }
    public static function tableName(): string
    {
        return 'order_items'; // Replace 'orders' with the actual name of your orders table
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getProductLang(string $lang)
    {
        return $this->hasOne(ProductLang::class, ['product_id' => 'product_id'])
            ->where(['lang' => $lang]);
    }
}
