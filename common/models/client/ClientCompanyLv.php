<?php

namespace common\models\client;

use yii\db\ActiveRecord;

class ClientCompanyLv extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_company_lv';
    }

    public function getOwners()
    {
        return $this->hasMany(ClientCompanyOwner::class, ['client_lv_number_id' => 'id']);
    }
}
