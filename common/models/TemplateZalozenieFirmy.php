<?php

namespace common\models;

use yii\db\ActiveRecord;

class TemplateZalozenieFirmy extends ActiveRecord
{
    public static function tableName()
    {
        return 'template_zalozenie_firmy';
    }
}
