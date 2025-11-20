<?php
namespace common\models;

use yii\db\ActiveRecord;

class TemplateVarsRows extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_vars_rows';
    }

    public static function exists(string $name)
    {
        return self::find()
            ->andWhere(['=','name',$name])
            ->count();
    }
}