<?php
namespace common\models\client;

use yii\db\ActiveRecord;

class ClientBusinessSubject extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_business_subject';
    }
}