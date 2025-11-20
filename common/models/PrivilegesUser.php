<?php

namespace app\models;

use yii\db\ActiveRecord;

class PrivilegesUser extends ActiveRecord
{
    public static function tableName()
    {
        return 'privilegesUsers';
    }
}
