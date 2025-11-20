<?php

namespace app\models;

use yii\db\ActiveRecord;

class Privilege extends ActiveRecord
{
    public static function tableName()
    {
        return 'privileges';
    }
}
