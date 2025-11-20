<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\models\ContractTemplate;

class ContractTemplateController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $templates = ContractTemplate::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('index', ['templates' => $templates]);
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (empty($_FILES['pdfTemplate']) || $_FILES['pdfTemplate']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No file uploaded.');
            }

            $file = $_FILES['pdfTemplate'];
            $uploadDir = Yii::getAlias('@backend/web/uploads/contracts/');
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $fileName = time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('Could not save file.');
            }

            $template = new ContractTemplate([
                'name' => pathinfo($file['name'], PATHINFO_FILENAME),
                'file_path' => '/uploads/contracts/' . $fileName
            ]);
            $template->save();

            return ['status' => 'ok', 'name' => $template->name];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
