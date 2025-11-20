<?php

namespace common\models;

use yii\db\ActiveRecord;

class Office extends ActiveRecord
{
    public const ACTIVE = 1;
    public static function tableName(): string
    {
        return 'office';
    }

    public function getAccounts()
    {
        return $this->hasMany(OfficeAccounts::class, ['office_id' => 'id']);
    }

    public function getFullAddress()
    {
        return $this->address . ", " . $this->zip . " " . $this->town;
    }
}