<?php
namespace common\models\client;

use common\models\Stat;
use yii\db\ActiveRecord;

class ClientCompanyInfo extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_company_info';
    }

    public function getCountry()
    {
        return $this->hasOne(Stat::class, ['id'=>'country_id']);
    }

    public function getFullName(): string
    {
        return "{$this->name} {$this->appendix}";
    }

    public function getFullAddress(): string
    {
        return "{$this->street_name} {$this->property_number}, {$this->zip} {$this->town}";
    }
}