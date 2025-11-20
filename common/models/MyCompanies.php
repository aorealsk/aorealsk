<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class MyCompanies extends ActiveRecord
{
    public static function tableName()
    {
        return 'myCompanies'; // ✅ exact table name from your DB
    }

    public function rules()
    {
        return [
            [['company_name', 'address', 'zip', 'town', 'ICO', 'DIC', 'DICDPH', 'CEO', 'DELEGATE', 'email', 'phone', 'iban', 'bank_name'], 'safe'],
        ];
    }
}
