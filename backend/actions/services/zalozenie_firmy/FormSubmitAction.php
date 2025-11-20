<?php

namespace backend\actions\services\zalozenie_firmy;

use Yii;
use yii\base\Action;
use common\models\client\Client;
use common\models\client\ClientOther;
use common\models\client\ClientRequest;
use common\models\client\ClientZivnost;
use common\models\client\ClientDocuments;
use common\models\client\ClientCompanyInfo;
use common\models\client\ClientPersonalInfo;
use common\models\client\ClientDocumentFiles;
use common\models\client\ClientBusinessSubject;
use common\models\idcardreader\CardProcessorFactory;

class FormSubmitAction extends Action
{
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $clientRequestData = Yii::$app->request->post('ClientRequest');
            $clientCompanyData = Yii::$app->request->post('ClientCompany');
            $spolocnikData = Yii::$app->request->post('ClientPersonalInfo');
            $konateliaData = Yii::$app->request->post('Konatelia');
            $clientBusinessSubjectData = Yii::$app->request->post('ClientBusinessSubject');
            $clientZivnostData = Yii::$app->request->post('ClientZivnost');
            $spravcaVkladuData = Yii::$app->request->post('SpravcaVkladu');
            $ownerData = Yii::$app->request->post('Owner');

            $client = new Client();
            $client->save();

            $spravcaFiles = $_FILES['SpravcaFiles'];
            if ($spravcaFiles['name']['predna-strana'] != '') {
                $documentId =  Yii::$app->request->post('SpravcaDocId');
                $this->uploadFiles($documentId, $client, $spravcaFiles);
            }

            $spolocnikFiles = $_FILES['SpolocnikFiles'];
            if ($spolocnikFiles['name']['predna-strana'] != '') {
                $documentId =  Yii::$app->request->post('SpolocniciDocId');
                $this->uploadFiles($documentId, $client, $spolocnikFiles);
            }

            $konateliaFiles = $_FILES['KonateliaFiles'];
            if ($konateliaFiles['name']['predna-strana'] != '') {
                $documentId =  Yii::$app->request->post('KonateliaDocId');
                $this->uploadFiles($documentId, $client, $konateliaFiles);
            }

            $zastupcaFiles = $_FILES['ZastupcaFiles'];
            if ($zastupcaFiles['name']['predna-strana'] != '') {
                $documentId =  Yii::$app->request->post('SpravcaDocId');
                $this->uploadFiles($documentId, $client, $zastupcaFiles);
            }

            $owner = new ClientPersonalInfo();
            foreach ($ownerData as $col => $val) {
                $owner->client_id = $client->id;
                $owner->$col = $val;
                $owner->client_type = 'majitel';
                $owner->save();
            }

            foreach ($spravcaVkladuData as $col => $val) {
                $spravcaVkladu = new ClientOther();
                $spravcaVkladu->client_id = $client->id;
                $spravcaVkladu->field_name = $col;
                $spravcaVkladu->field_value = $val;
                $spravcaVkladu->save();
            }

            $clientRequest = new ClientRequest();
            foreach ($clientRequestData as $col => $val) {
                $clientRequest->$col = $val;
                $clientRequest->client_id = $client->id;
            }
            $clientRequest->save();

            $clientCompany = new ClientCompanyInfo();
            foreach ($clientCompanyData as $col => $val) {
                $clientCompany->$col = $val;
                $clientCompany->client_id = $client->id;
            }
            $clientCompany->save();

            foreach ($spolocnikData as $field) {
                $clientPersonalInfo = new ClientPersonalInfo();
                $clientPersonalInfo->client_type = 'spolocnik';
                $clientPersonalInfo->client_id = $client->id;
                foreach ($field as $col => $val) {
                    $clientPersonalInfo->$col = $val;
                }
                $clientPersonalInfo->save();
            }

            foreach ($konateliaData as $field) {
                $clientPersonalInfo = new ClientPersonalInfo();
                $clientPersonalInfo->client_id = $client->id;
                $clientPersonalInfo->client_type = 'konatel';
                foreach ($field as $col => $val) {
                    $clientPersonalInfo->$col = $val;
                }
                $clientPersonalInfo->save();
            }

            foreach ($clientBusinessSubjectData as $field) {
                $businessSubject = new ClientBusinessSubject();
                $businessSubject->client_id = $client->id;
                foreach ($field as $col => $val) {
                    $businessSubject->$col = $val;
                }
                $businessSubject->save();
            }

            foreach ($clientZivnostData as $field) {
                $clientZivnost = new ClientZivnost();
                $clientZivnost->client_id = $client->id;
                foreach ($field as $col => $val) {
                    $clientZivnost->$col = $val;
                }
                $clientZivnost->save();
            }
        }

        return $this->controller->redirect('/backoffice/services/index');
    }

    private function uploadFiles($documentId, $client, $files)
    {
        $path = Yii::getAlias('@webroot') . "/../../clients/";

        move_uploaded_file($files['tmp_name']['predna-strana'], $path . $files['name']['predna-strana']);
        move_uploaded_file($files['tmp_name']['zadna-strana'], $path . $files['name']['zadna-strana']);

        $document = CardProcessorFactory::getDocument($documentId);
        $document->pridajPrednuStranu($path . $files['name']['predna-strana']);
        $document->pridajZadnuStranu($path . $files['name']['zadna-strana']);
        $result = $document->processDocument('Y-m-d');

        $clientDocs = new ClientDocuments();
        $clientDocs->client_id =  $client->id;
        $clientDocs->doc_type =  $result['doc_type'];
        $clientDocs->doc_number =  $result['doc_number'];
        $clientDocs->doc_issuer =  $result['doc_issuer'];
        $clientDocs->issue_date =  $result['issue_date'];
        $clientDocs->validity_date =  $result['validity_date'];
        $clientDocs->save();

        //predna strana
        $clientDocFile = new ClientDocumentFiles();
        $clientDocFile->doc_id = $documentId;
        $clientDocFile->file = $files['name']['predna-strana'];
        $clientDocFile->side = 0;
        $clientDocFile->save();

        //zadna strana
        $clientDocFile = new ClientDocumentFiles();
        $clientDocFile->doc_id = $documentId;
        $clientDocFile->file = $files['name']['zadna-strana'];
        $clientDocFile->side = 1;
        $clientDocFile->save();
    }
}
