<?php
namespace common\models;

use yii\db\ActiveRecord;

class TemplateVarsMap extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_vars_map';
    }

    public function getFullMap():array
    {
        $rowCount = TemplateVarsRows::find()->count();
        $colCount = TemplateVarsCols::find()->count();
        $result = array_fill(0,$rowCount,array_fill(0,$colCount,0));

        $map = self::find()->all();
        array_walk($map, function($val, $key) use(&$result) {
            $result[$val['row_id']][$val['col_id']] = $val['status'];
        });

        return $result;
    }
}