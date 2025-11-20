<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class Seat extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2; // Assuming 'db2' is the name of your DB2 connection component
    }
    public static function tableName(): string
    {
        return 'seats'; // Replace 'orders' with the actual name of your orders table
    }
}
