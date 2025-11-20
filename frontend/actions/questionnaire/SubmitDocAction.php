<?php

namespace frontend\actions\questionnaire;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\models\idcardreader\CardProcessorFactory;

class SubmitDocAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($_FILES)) {
            $docType = Yii::$app->request->post('docType');
            
            $document = CardProcessorFactory::getDocument($docType);
            $document->pridajPrednuStranu($_FILES['prednaStrana']['tmp_name']);
            $document->pridajZadnuStranu($_FILES['zadnaStrana']['tmp_name']);

            return $document->processDocument('Y-m-d');
        }
    }
}
