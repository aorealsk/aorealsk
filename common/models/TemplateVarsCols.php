<?php
namespace common\models;

use yii\db\ActiveRecord;

class TemplateVarsCols extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_vars_cols';
    }

    public static function exists(string $prefix, string $postfix)
    {
        return self::find()
            ->andWhere(['=','prefix',$prefix])
            ->andWhere(['postfix',$postfix])
            ->count();
    }
}