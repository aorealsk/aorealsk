<?php
namespace common\models\promo;

use yii\db\ActiveQuery;

class StockItem extends \yii\db\ActiveRecord
{
    const DECREASE = -1;
    const INCREASE = 1;
    public static function tableName(): string
    {
        return 'stock_item';
    }

    public function getTitles(): ActiveQuery
    {
        return $this->hasMany(StockItemLang::class, ['stock_item_id'=>'id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(StockItemGroupLang::class, ['stock_item_group_id'=>'group_id']);
    }

    public function getTitle(string $lang='sk')
    {
        return StockItemLang::find()
            ->select(['title'])
            ->andWhere(['=','stock_item_id',$this->id])
            ->andWhere(['=','lang',$lang])
            ->scalar();
    }

    public function getDescription(string $lang='sk')
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
            $bottles = (int)($amount/$this->bottle_size);
            $this->bottle_cnt += $op * $bottles;
        }
        $this->amount += $op * $amount;
        $this->save();
    }

    public function isBottleOrBundleSell()
    {
        return in_array($this->sell_unit,['bundle','bottle']);
    }

    public function isBundleSell()
    {
        return $this->sell_unit == 'bundle';
    }

}