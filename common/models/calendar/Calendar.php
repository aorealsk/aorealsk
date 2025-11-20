<?php

namespace common\models\calendar;

use yii\db\ActiveRecord;

class Calendar extends ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const SYSTEM_CALENDAR = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'calendar';
    }

    public function rules(): array
    {
        return [
            [['title', 'user_id', 'status'], 'required'],
            [['status', 'user_id', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Názov',
            'status' => 'Stav',
            'user_id' => 'Používateľ',
        ];
    }
}
