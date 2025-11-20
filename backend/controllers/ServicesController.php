<?php

namespace backend\controllers;

use common\models\Sluzby;
use common\models\sys\SysLog;
use Yii;
use yii\web\Response;
use yii\web\Controller;

class ServicesController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
//            'index' => [
//                'class' => 'backend\actions\services\zalozenie_firmy\IndexAction',
//            ],
//            'pozicky' => [
//                'class' => 'backend\actions\services\pozicka\IndexAction'
//            ],
            'index' => [
                'class' => 'backend\actions\services\ServiceListAction',
            ],
//            'settings' => [
//                'class' => 'backend\actions\services\SettingsAction'
//            ],
            'edit' => [
                'class' => 'backend\actions\services\EditServiceAction'
            ],
            'add' => [
                'class' => 'backend\actions\services\AddServiceAction'
            ],
//            'pricelist' => [
//                'class' => 'backend\actions\services\pricelist\IndexAction'
//            ],
//            'price-list-edit' => [
//                'class' => 'backend\actions\services\pricelist\PriceListEditAction'
//            ],
//             'dotaznik' => [
//                'class' => 'backend\actions\services\zalozenie_firmy\DotaznikAction'
//            ],
//            'form-submit' => [
//                'class' => 'backend\actions\services\zalozenie_firmy\FormSubmitAction'
//            ]
        ];
    }

//    public function actionUpdatePriceList()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $list = Yii::$app->request->post('list');
//        return [
//            'status' => 'ok'
//        ];
//    }


    /**
     * @return string[]
     */
    public function actionChangeServiceStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
        $serviceId = Yii::$app->request->post('service_id');
        $service = Sluzby::findOne(['id' => $serviceId]);
        if (!$service) {
            return ['status' => 'error', 'message' => Yii::t('app', 'Služba nebola nájdená!')];
        }
        $tr = Yii::$app->db->beginTransaction();
        try {
            $service->status = $status;
            $service->save();
            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            SysLog::WriteError(null, __CLASS__, $e->getTraceAsString(), __LINE__);
            return ['status' => 'error','message' => $e->getTraceAsString()];
        }
        return [
            'status' => 'ok',
            'message' => Yii::t('app', 'Status služby bol úspešne zmenený!')

        ];
    }

    /**
     * @param $id
     * @return string|Response
     */
//    public function actionEdit($id)
//    {
//        $clientCompany = ClientCompanyInfo::find()->where(['=', 'client_id', $id])->one();
//        $spolocnici = ClientPersonalInfo::find()->where(['=', 'client_id', $id])->andWhere(['=', 'client_type', 'spolocnik'])->all();
//        $konatelia = ClientPersonalInfo::find()->where(['=', 'client_id', $id])->andWhere(['=', 'client_type', 'konatel'])->all();
//        if (Yii::$app->request->isPost) {
//
//            $companyData = Yii::$app->request->post('Company');
//            foreach ($companyData as $col => $val) {
//                $clientCompany->$col = $val;
//            }
//            $clientCompany->save();
//
//            $spolocnici = Yii::$app->request->post('Spolocnik');
//            foreach ($spolocnici as $spolocnikData) {
//                $spolocnik = ClientPersonalInfo::find()->where(['=', 'id', $spolocnikData['id']])->andWhere(['=', 'client_type', 'spolocnik'])->one();
//                foreach ($spolocnikData as $col => $val) {
//                    $spolocnik->$col = $val;
//                }
//                $spolocnik->save();
//            }
//
//            $konateliaData = Yii::$app->request->post('Konatel');
//            foreach ($konateliaData as $konatelData) {
//                $konatel = ClientPersonalInfo::find()->where(['=', 'id', $konatelData['id']])->andWhere(['=', 'client_type', 'konatel'])->one();
//                foreach ($konatelData as $col => $val) {
//                    $konatel->$col = $val;
//                }
//                $konatel->save();
//            }
//
//            return $this->redirect('/backoffice/services/index');
//        }
//
//        return $this->render('/services/zalozenie-firmy/edit', [
//            'company' => $clientCompany,
//            'spolocnici' => $spolocnici,
//            'konatelia' => $konatelia
//        ]);
//    }

//    public function actionDocuments($id)
//    {
//        if (is_null(Yii::$app->user->identity)) {
//            return $this->redirect(Url::to(['/site/login']));
//        }
//
//        $templateIds = [
//            '476',
//            '477',
//            '478',
//            '479',
//            '481',
//            '482',
//            '483',
//            '484',
//            '485',
//            '505',
//            '506',
//            '507'
//        ];

//        $templates = [];
//        foreach ($templateIds as $templateId) {
//            $doc = TemplateFactory::getDocument($templateId);
//            $templates[$templateId] = $doc->getTemplateName();
//        }
//
//        return $this->render('/services/zalozenie-firmy/documents', [
//            'templates' => $templates,
//            'clientId' => $id,
//            'offices' => Office::find()->all()
//        ]);
//    }

//    public function actionDownloadFile()
//    {
//        $clientId = Yii::$app->request->post('clientId');
//        $templateId = Yii::$app->request->post('templateId');
//
//        $document = TemplateFactory::getDocument((int) $templateId);
//        $document->enablePageNumbering(PdfTemplateDocument::PAGE_NUMBER_CENTER);
//
//        $pdf = new PdfTemplateDocument();
//        //$pdf->enablePageNumbering(PdfTemplateDocument::PAGE_NUMBER_CENTER);
//        $template = TemplateContent::find()
//            ->where(['=', 'student_id', $clientId])
//            ->andWhere(['=', 'template_id', $templateId])
//            ->orderBy(['created_at' => SORT_DESC])
//            ->one();
//
//        if ($template != null) {
//            $pdf->setTemplateContent($template->content);
//            $template->delete();
//        } else {
//            $document->setClientInfo($clientId);
//            $content = $document->process();
//            $pdf->setTemplateContent($content);
//        }
//
//        //$fileName = 'print-' . uniqid() . '.pdf';
//        //$document->saveFileToDir($pdf->getTemplateContent(), $fileName, $clientId);
//        $document->downloadFile($pdf->getTemplateContent());
//        //$pdf->downloadFile();
//    }
//
//    public function actionDownloadAll()
//    {
//        $templateIds = Yii::$app->request->post('templateId');
//        $clientId = Yii::$app->request->post('clientId');
//
//        $zip = new ZipArchive();
//        $filename = 'dokumenty' . (new DateTimeImmutable())->format('YmdHis') . '.zip';
//        $zip->open($filename, ZipArchive::CREATE);
//
//        foreach ($templateIds as $templateId) {
//            $document = TemplateFactory::getDocument((int) $templateId);
//            $document->setClientInfo($clientId);
//            $document->enablePageNumbering(PdfTemplateDocument::PAGE_NUMBER_CENTER);
//            $pdf = new PdfTemplateDocument();
//
//            $template = TemplateContent::find()->where(['=', 'student_id', $clientId])->andWhere(['=', 'template_id', $templateId])->orderBy(['created_at' => SORT_DESC])->one();
//
//            if ($template != null) {
//                $pdf->setTemplateContent($template->content);
//                $template->delete();
//            } else {
//                $content = $document->process();
//                $pdf->setTemplateContent($content);
//            }
//            $fileName = iconv('UTF-8',
//                'ASCII//TRANSLIT',
//                str_replace([" ","/"],"_",$document->getTemplateName()."_".time().".pdf"));
//            //$fileName = 'print-' . uniqid() . '.pdf';
//            $document->saveFileToDir($pdf->getTemplateContent(), $fileName, $clientId);
//            $zip->addFile(Yii::getAlias('@webroot') . "/../../docs/zalozenie-firmy/" . $fileName, $fileName);
//        }
//
//        $zip->close();
//
//        ob_end_clean();
//        header("Content-type: application/zip");
//        header("Content-Disposition: attachment; filename=$filename");
//        header("Content-length: " . filesize($filename));
//        header("Pragma: no-cache");
//        header("Expires: 0");
//        readfile($filename);
//        unlink($filename);
//
//        Yii::$app->end();
//    }

//    public function actionViewFile($templateId, $clientId)
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $template = TemplateFactory::getDocument((int) $templateId);
//        $template->setClientInfo($clientId, null);
//        $content = $template->process();
//        /*$pdf = new PdfTemplateDocument();
//        $pdf->setTemplateContent($content);*/
//
//        return [
//            'content' => $content,
//            'clientInfo' => ClientPersonalInfo::find()->where(['=', 'client_id', $clientId])->all(),
//            'sluzba' => Sluzby::findOne(['nazov' => 'Odplata'])
//        ];
//    }
//
//    public function actionViewTemplate()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $data = Yii::$app->request->post();
//
//        $template = TemplateFactory::getDocument((int)$data['tid']);
//        $template->setClientInfo($data['cid'],null);
//        $content = $template->viewTemplate();
//        $result = $this->renderPartial('zalozenie-firmy/document_wrapper',
//            [
//                'template_content' => $content
//            ]
//        );
//
//        return [
//            'status' => 'ok',
//            'view_content' => $result,
//        ];
//    }
//
//    public function actionSaveTemplate()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $data = Yii::$app->request->post();
//        $template = TemplateFactory::getDocument((int) $data['templateId']);
//
//        $template
//            ->setClientInfo($data['clientId'])
//            ->setAdditionalInfo($data)
//            ->setTownOfSignature($data['town'])
//            ->setDateOfSignature($data['date']);
//
//        $content = $template->process();
//
//        $pdf = new PdfTemplateDocument();
//        $pdf->setTemplateContent($content);
//
//        $templateContent = new TemplateContent();
//        $templateContent->student_id = $data['clientId'];
//        $templateContent->template_id = $data['templateId'];
//        $templateContent->content = $content;
//        $templateContent->save();
//
//        return ['status' => 200];
//    }

//    public function actionSaveChanges()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $data = Yii::$app->request->post();
//        $template = TemplateFactory::getDocument((int) $data['templateId']);
//        $template->setTownOfSignature($data['town'])->setDateOfSignature($data['date'])->setIncorporationDate($data['incorporationDateValue']);
//        $template->setClientInfo(
//            $data['clientId'],
//            $data['splnomocnenecName'],
//            $data['lawyerName'],
//            $data['lawyerResidence'],
//            $data['lawyerIco'],
//            $data['lawyerRegistrationNumber'],
//            $data['lawyerEmail'],
//            $data['serviceProvider'],
//            $data['splnomocnenecSsn'],
//            $data['splnomocnenecAddress'],
//            $data['splnomocnenecBirth'],
//            $data['splnomocnenecDocName'],
//            $data['splnomocnenecDocAddress'],
//            $data['splnomocnenecDocZip'],
//            $data['splnomocnenecDocTown'],
//            $data['officeIban'],
//            $data['bankName'],
//            $data['odplataCena'],
//            $data['odplataDph'],
//            $data['providerDeputy']
//        )->setSelectedClient($data['selectedClientId']);
//        $content = $template->process();
//
//        $pdf = new PdfTemplateDocument();
//        $pdf->setTemplateContent($content);
//
//        $templateContent = new TemplateContent();
//        $templateContent->student_id = $data['clientId'];
//        $templateContent->template_id = $data['templateId'];
//        $templateContent->content = $content;
//        $templateContent->save();
//
//        return [
//            'status' => '200'
//        ];
//    }
//
//    public function actionFetchClientPersonalInfo($clientId)
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        return ClientPersonalInfo::findOne($clientId);
//    }
//
//    public function actionFetchServiceProviderBankInfo()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $officeId = Yii::$app->request->post('officeId');
//        $account = OfficeAccounts::findOne($officeId);
//
//        return [
//            'account' => $account,
//            'bank' => FinancialInstitution::findOne($account->bank_id)
//        ];
//    }
}
