<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class ContractTemplate extends ActiveRecord
{
    public static function tableName()
    {
        return 'contract_templates';
    }

    public function rules()
    {
        return [
            [['name', 'file_path'], 'required'],
            [['name', 'file_path'], 'string', 'max' => 255],
        ];
    }
}
