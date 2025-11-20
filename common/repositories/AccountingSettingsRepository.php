<?php

namespace common\repositories;

use common\models\AccountingSettings;

final class AccountingSettingsRepository
{
    public static function getVatLimit(string $date): ?float
    {
        $item = AccountingSettings::find()
            ->andWhere(['field_name' => 'vat_limit'])
            ->andWhere(['<=', 'valid_from', $date])
            ->andWhere(['is', 'valid_to', null])
            ->one();
        if (!$item) {
            return null;
        }

        return (float)$item['field_value'];
    }
}