<?php

namespace common\models\client;

use yii\db\ActiveRecord;

class ClientOther extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_other';
    }
}
