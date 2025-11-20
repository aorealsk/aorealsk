<?php
namespace common\models\client;

use yii\db\ActiveRecord;

class ClientPersonalInfo extends ActiveRecord
{
    public static function tableName()
    {
        return 'client_personal_info';
    }

    public function getFullName(): string
    {
        $name = [];
        if (!is_null($this->adegree_before)) {
            $name[] = $this->adegree_before;
        }
        $name[] = $this->first_name;
        $name[] = $this->last_name;
        if (!is_null($this->adegree_after)) {
            $name[] = $this->adegree_after;
        }

        return implode(' ', $name );
    }

    public function getFullAddress()
    {
        return "{$this->street_name} {$this->property_number}, {$this->zip} {$this->town}";
    }

    public function getBusinesses()
    {
        return $this->hasMany(ClientBusinessSubject::class, ['client_id'=>'client_id']);
    }
}