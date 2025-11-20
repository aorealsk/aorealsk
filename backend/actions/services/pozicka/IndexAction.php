<?php

namespace backend\actions\services\pozicka;

use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        return $this->controller->render('pozicka/index');
    }
}
