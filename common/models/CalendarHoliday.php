<?php

namespace common\models;

use yii\db\ActiveRecord;

class CalendarHoliday extends ActiveRecord
{
    public static function tableName()
    {
        return 'calendar_holiday';
    }

    public function rules()
    {
        return [
            [['day', 'month', 'name', 'category'], 'required'],
            [['day', 'month'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['category'], 'string', 'max' => 50],
        ];
    }
}
