<?php

namespace common\models\partners;

class PartnerContactPersons extends \yii\db\ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public static function tableName(): string
    {
        return 'partner_contact_persons';
    }
    public function rules(): array
    {
        return [];
    }
}
