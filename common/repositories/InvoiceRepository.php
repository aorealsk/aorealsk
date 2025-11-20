<?php

namespace common\repositories;

use common\helpers\DateHelper;
use Yii;

class InvoiceRepository
{
    public static function getInvoiceTotalInvoiceSumByYear(int $company, int $year): float
    {
        $query = "
            select
                sum(f.suma) as price
            from 
                faktura_dodavatel fd
            join
                faktura f on f.id=fd.faktura_id
            where
                fd.dodavatel_id=:cid and f.datum_vystavenia between :fromdate and :todate
        ";

        $result = Yii::$app
            ->db
            ->createCommand($query)
            ->bindValues([
                ':cid' => $company,
                ':fromdate' => $year . '-01-01',
                ':todate' => $year . '-12-31'
            ])
            ->queryScalar();

        return (float)$result ?? 0.0;
    }

    public static function getInvoiceTotalInvoiceSumByLastTwelveMonth(int $year, int $month, int $company): float
    {

        $query = "
            select
                sum(f.suma) as price
            from 
                faktura_dodavatel fd
            join
                faktura f on f.id=fd.faktura_id
            where
                fd.dodavatel_id=:cid and f.datum_vystavenia between :fromdate and :todate
        ";

        $result = Yii::$app
            ->db
            ->createCommand($query)
            ->bindValues([
                ':cid' => $company,
                ':fromdate' => "$year-$month-01",
                ':todate' => DateHelper::getToday()
            ])
            ->queryScalar();

        return (float)$result ?? 0.0;

        return 0.0;
    }
}
