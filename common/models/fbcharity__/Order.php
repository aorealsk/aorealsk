<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class Order extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2; // Assuming 'db2' is the name of your DB2 connection component
    }
    public static function tableName(): string
    {
        return 'orders'; // Replace 'orders' with the actual name of your orders table
    }
    public function getItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getGuests()
    {
        return $this->hasMany(Guest::class, ['order_id' => 'id']);
    }

    public function getSeats()
    {
        return $this->hasMany(OrderSeat::class, ['order_id' => 'id']);
    }
}
