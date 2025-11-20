<?php

namespace frontend\actions\cuba;

use common\models\Applications;
use yii\base\Action;
use common\models\AcademicDegrees;
use common\models\Stat;

class RegistrationAction extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        $degrees = new AcademicDegrees();
        $countries = new Stat();

        return $this->controller->render('register/index',[
            'titul_pred' => $degrees->getTitulPredMenom(),
            'prefixes' => $countries->getPrefixes(),
            'countries' => $countries->getCountries(),
            'professions' => Applications::find()->select(['id','title'])->asArray()->all(),
        ]);
    }
}