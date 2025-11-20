<?php

namespace common\models;

use yii\db\ActiveRecord;

class Units extends ActiveRecord
{
    public const ACTIVE = 1;

    public static function tableName()
    {
        return 'units';
    }
    public function rules()
    {
        return [];
    }
}
