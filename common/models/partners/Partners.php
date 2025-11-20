<?php

namespace common\models\partners;

use yii\db\ActiveRecord;

class Partners extends ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 0;

    public static function tableName(): string
    {
        return 'partners';
    }
}
