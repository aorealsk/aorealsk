<?php
namespace common\models\promo;
class ComboItemTranslator
{
    private $items;
    private $lang;

    public function __construct(string $items, string $lang)
    {
        $this->items = json_decode($items,true);
        $this->lang = $lang;
    }

    public function getSelectItems()
    {
        $where = '';
        $result = [];
        foreach ($this->items as $key => $item) {
            if ($key === 'main' && count($this->items) == 1) {
                if (!empty($item['item_type']) && $item['item_type'] === 'group') {
                    $where = 'group_id in (' . implode(',',$item['item']) . ')';
                } else {
                    $where = "id in ({$item['item']})";
                }
            }
            if ($key === 'ext') {
                if (!empty($item[0]['item_type']) && $item[0]['item_type'] === 'item') {
                    $where = 'id in (' . implode(',',$item[0]['item']).')';
                } else {
                    $where = 'group_id in (' . implode(',',$item[0]['item']).')';
                }
            }
        }

        $stocks = StockItem::find()->where($where)->all();
        foreach ($stocks as $stock) {
            $result[] = [
                'value' => $stock->id,
                'text' => $stock->getTitle(),
            ];
        }
        return $result;
    }
}