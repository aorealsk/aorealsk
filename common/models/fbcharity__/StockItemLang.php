<?php

namespace common\models\fbcharity;

use Yii;
use yii\db\ActiveRecord;

class StockItemLang extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_item_langs';
    }

    public static function getDb()
    {
        return Yii::$app->db2; //
    }
}