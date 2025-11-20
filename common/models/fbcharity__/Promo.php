<?php

namespace common\models\fbcharity;

use Yii;
use yii\db\ActiveRecord;

class Promo extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public static function tableName(): string
    {
        return 'promos';
    }
}
