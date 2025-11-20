<?php

namespace backend\actions\accounting;

use common\repositories\InvoiceRepository;
use Yii;
use yii\base\Action;
use yii\helpers\Url;
use common\models\Office;
use common\helpers\DateHelper;

class InvoiceAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $year = DateHelper::getActualYear();

        $officeList = Office::find()->select(['id','name'])->andWhere(['=','status',1])->asArray()->all();
        foreach ($officeList as &$office) {
            $office['invoices'] = Yii::$app->db->createCommand("
                SELECT
                    f.id, 
                    f.znak, f.rok, f.mesiac, f.cislo,
                    fo.kontaktna_osoba, 
                    f.typ_faktury, 
                    fo.nazov AS 'odberatel',
                    f.k_uhrade,  
                    f.zaloha,
                    f.status, 
                    f.splatnost, 
                    f.datum_vystavenia,
                    f.bookedIn   
                FROM 
                    faktura f
                JOIN
                    faktura_dodavatel fd ON fd.faktura_id=f.id
                JOIN
                    faktura_odberatel fo ON fo.faktura_id=f.id
                WHERE 
                    fd.dodavatel_id = {$office['id']}
                ORDER BY
                    f.id DESC
            ")->queryAll();
        }

        return $this->controller->render('invoice/index', [
            'offices' => $officeList,
            'year' => $year,
            'invoiceSummary' => $this->getInvoceSummary($officeList),
        ]);
    }

    protected function getInvoceSummary(array $companies): array
    {
        $result = [];
        $currentYear = date('Y');
        $before = explode('-', date('Y-m-01', strtotime('-12 months')));

        foreach ($companies as $company) {
            $result[$company['id']] = [
                'yearly_total' => InvoiceRepository::getInvoiceTotalInvoiceSumByYear($company['id'], $currentYear),
                'last_12m_total' => InvoiceRepository::getInvoiceTotalInvoiceSumByLastTwelveMonth(
                    $before[0],
                    $before[1],
                    $company['id']
                ),
            ];
        }

        return $result;
    }

}
