<?php

namespace common\repositories;

use common\models\Office;

final class OfficeRepository
{
    public static function getAllActiveAsArray(): array
    {
        return Office::find()->where(['=', 'status', Office::ACTIVE])->asArray()->all();
    }
}