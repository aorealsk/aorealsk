<?php

namespace common\models\partners;

use yii\db\ActiveRecord;

class PartnerDetails extends ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const  STATUS_INACTIVE = 0;

    public static function tableName(): string
    {
        return 'partner_details';
    }
}
