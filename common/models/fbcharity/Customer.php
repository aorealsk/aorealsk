<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return 'customers'; // Replace 'customers' with your actual table name
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
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['customer_id' => 'id']);
    }
}
