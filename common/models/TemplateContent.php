<?php

namespace common\models;

use yii\db\ActiveRecord;

class TemplateContent extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_content';
    }
}
