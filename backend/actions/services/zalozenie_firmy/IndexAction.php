<?php

namespace backend\actions\services\zalozenie_firmy;

use common\models\client\Client;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $clients = Client::find()->all();
        return $this->controller->render('zalozenie-firmy/index', [
            'clients' => $clients
        ]);
    }
}
