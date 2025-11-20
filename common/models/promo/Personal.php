<?php
namespace common\models\promo;

class Personal extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'personal';
    }

    public function getFullName()
    {
        return $this->name_first . ' ' . $this->name_last;
    }
}