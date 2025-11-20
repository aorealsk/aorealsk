<?php

namespace frontend\actions\questionnaire;

use common\models\AcademicDegrees;
use yii\base\Action;
use common\models\FinancialInstitutionText;

class IndexAction extends Action
{
    public function run()
    {
        return $this->controller->render('index', [
            'cust_docs'     =>  FinancialInstitutionText::find()
                ->select(['id', 'internal_text'])
                ->andWhere(['is', 'deleted_at', null])
                ->andWhere(['=', 'category', FinancialInstitutionText::CUSTDOCS])
                ->asArray()->all(),
            'degrees' =>    AcademicDegrees::find()->all()
        ]);
    }
}
