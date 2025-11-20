<?php

namespace common\models\client;

use yii\db\ActiveRecord;

class ClientCompanyOwner extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_company_owner';
    }
}
