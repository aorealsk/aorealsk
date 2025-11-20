<?php

namespace backend\actions\services\zalozenie_firmy;

use yii\base\Action;
use common\models\FinancialInstitutionText;

class DotaznikAction extends Action
{
    public function run()
    {
        return $this->controller->render('zalozenie-firmy/dotaznik', [
                'cust_docs' =>  FinancialInstitutionText::find()
                ->select(['id', 'internal_text'])
                ->andWhere(['is', 'deleted_at', null])
                ->andWhere(['=', 'category', FinancialInstitutionText::CUSTDOCS])
                ->asArray()->all(),
        ]);
    }
}
