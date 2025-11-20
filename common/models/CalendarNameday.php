<?php

namespace common\models;

use yii\db\ActiveRecord;

class CalendarNameday extends ActiveRecord
{
    public static function tableName()
    {
        return 'calendar_nameday';
    }

    public function rules()
    {
        return [
            [['day', 'month', 'names'], 'required'],
            [['day', 'month'], 'integer'],
            [['names'], 'string', 'max' => 255],
        ];
    }
}
