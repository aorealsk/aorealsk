<?php

namespace common\models\fbcharity;

use common\models\fbcharity\StockItem;
use Yii;
use yii\db\ActiveRecord;

class StockItemGroup extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_item_groups';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getLangs()
    {
        return $this->hasMany(StockItemGroupLang::class, ['stock_item_group_id' => 'id']);
    }

    public function getItems()
    {
        return $this->hasMany(StockItem::class, ['group_id' => 'id']);
    }

}
