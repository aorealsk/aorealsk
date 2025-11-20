<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class StockItemGroupLang extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_item_group_langs';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }
}
