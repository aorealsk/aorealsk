<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class StockItemMedia extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_item_media';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }
}