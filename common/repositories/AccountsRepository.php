<?php

namespace common\repositories;

use common\models\OfficeAccounts;
use Yii;

class AccountsRepository
{
    public static function getAllByCompanyId(int $companyId): array
    {
        $sql = "
            select 
                oa.id, oa.iban, oa.currency, oa.valid_from, oa.valid_to,
                fi.name as bank_name
            from
                office_accounts oa
            join
                financial_institution fi on fi.id=oa.bank_id
            where
                oa.office_id=$companyId
        ";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function save(array $data, OfficeAccounts $account) : bool
    {
        $account->setAttributes($data);
        return $account->save();
    }
}