<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class OrderSeat extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2; // Assuming 'db2' is the name of your DB2 connection component
    }
    public static function tableName(): string
    {
        return 'order_seats'; // Replace 'orders' with the actual name of your orders table
    }
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
    public function getSeat()
    {
        return $this->hasOne(Seat::class, ['id' => 'seat_id']);
    }
}
