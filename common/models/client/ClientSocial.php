<?php
namespace common\models\client;

use yii\db\ActiveRecord;

class ClientSocial extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'client_social';
    }
}