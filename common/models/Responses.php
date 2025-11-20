<?php

namespace common\models;

use yii\db\ActiveRecord;

class Responses extends ActiveRecord
{
    public static function tableName()
    {
        return 'responses';
    }
}