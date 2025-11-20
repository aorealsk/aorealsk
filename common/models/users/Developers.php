<?php

namespace common\models\users;

use yii\db\ActiveRecord;

class Developers extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'developers';
    }
}