<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class PromoCustomer extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_customers';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }
}
