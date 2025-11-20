<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class TemplateBlock extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_blocks';
    }

    public function rules()
    {
        return [
            [['template_id', 'block_type', 'pos_x', 'pos_y'], 'required'],
            [['template_id'], 'integer'],
            [['pos_x', 'pos_y', 'width', 'height'], 'number'],
            [['font_size'], 'integer'],
            [['color'], 'string', 'max' => 20],
            [['block_type'], 'string', 'max' => 100],
        ];
    }
}
