<?php

namespace frontend\actions\questionnaire;

use yii\base\Action;

class ThankYouAction extends Action
{
    public function run()
    {
        return $this->controller->render('thank-you');
    }
}
