<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class ProductLang extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public static function tableName(): string
    {
        return 'product_langs';
    }
}