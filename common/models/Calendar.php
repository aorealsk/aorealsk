<?php
// common/models/Calendar.php
namespace common\models;

use yii\db\ActiveRecord;

class Calendar extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%calendar}}';
    }

    public function getEvents()
    {
        return $this->hasMany(CalendarEvent::class, ['calendar_id' => 'id']);
    }
}
