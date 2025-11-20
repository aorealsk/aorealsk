<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class PromoStock extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_stocks';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }
}
