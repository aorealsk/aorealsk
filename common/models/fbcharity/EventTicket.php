<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class EventTicket extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'event_tickets';
    }
    public static function getDb()
    {
        return Yii::$app->db2; // Replace 'db2' with your actual DB2 connection component name
    }
}
