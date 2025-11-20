<?php

namespace common\models\fbcharity;

use common\models\fbcharity\StockItemGroupLang;
use common\models\fbcharity\StockItemLang;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use Yii;

class StockItem extends ActiveRecord
{
    public const DECREASE = -1;
    public const INCREASE = 1;
    // Define the table name associated with this model
    public static function tableName(): string
    {
        return 'stock_items';
    }

    public static function getDb()
    {
        return Yii::$app->db2; // Assuming 'db2' is the name of your DB2 connection component
    }

    public function getTitles(): ActiveQuery
    {
        return $this->hasMany(StockItemLang::class, ['stock_item_id' => 'id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(StockItemGroupLang::class, ['stock_item_group_id' => 'group_id']);
    }

    public function getTitle(string $lang = 'sk')
    {
        return StockItemLang::find()
            ->select(['title'])
            ->andWhere(['=','stock_item_id',$this->id])
            ->andWhere(['=','lang',$lang])
            ->scalar();
    }

    public function getDescription(string $lang = 'sk')
    {
        return StockItemLang::find()
            ->select(['description'])
            ->andWhere(['=','stock_item_id',$this->id])
            ->andWhere(['=','lang',$lang])
            ->scalar();
    }

    public function updateAmount(float $amount, int $op)
    {
        // adjust the bottle count
        if ($this->bottle_size > 0) {
            $bottles = (int)($amount / $this->bottle_size);
            $this->bottle_cnt += $op * $bottles;
        }
        $this->amount += $op * $amount;
        $this->save();
    }

    public function isBottleOrBundleSell()
    {
        return in_array($this->sell_unit, ['bundle','bottle']);
    }

    public function isBundleSell()
    {
        return $this->sell_unit == 'bundle';
    }

    public function getPic()
    {
        return $this->hasOne(StockItemMedia::class, ['stock_item_id' => 'id']);
    }

    public function getPicUrl(): string
    {
        $pic = $this->getPic()->one();
        if (is_null($pic)) {
            return Yii::getAlias('@web') . '/../media/no-image.webp';
        }
        return Yii::getAlias('@web') . '/../media/stock/' . $pic['file_name'];
    }
}
