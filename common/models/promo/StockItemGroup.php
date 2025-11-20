<?php
namespace common\models\promo;

class StockItemGroup extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_item_group';
    }

    public function getTitle(string $lang='sk')
    {
        return StockItemGroupLang::find()
            ->select(['title'])
            ->andWhere(['=','stock_item_group_id',$this->id])
            ->andWhere(['=','lang',$lang])
            ->scalar();
    }

    public function getItems()
    {
        return $this->hasMany(StockItem::class, ['group_id'=>'id']);
    }
}